<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    /**
     * Columns the table may be sorted by.
     *
     * @var list<string>
     */
    public const array SORTABLE = ['name', 'email', 'created_at', 'updated_at'];

    /**
     * Allowed page sizes.
     *
     * @var list<int>
     */
    public const array PER_PAGE = [10, 25, 50];

    /**
     * List users with search, status filter, sorting and pagination.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $filters = $this->filters($request);
        $perPage = in_array($request->integer('per_page'), self::PER_PAGE, true) ? $request->integer('per_page') : self::PER_PAGE[0];

        $users = $this->filteredQuery($filters)
            ->orderBy($filters['sort'], $filters['direction'])
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'filters' => $filters,
            'perPage' => $perPage,
            'totalUsers' => User::count(),
            'statusCounts' => [
                'verified' => User::whereNotNull('email_verified_at')->count(),
                'unverified' => User::whereNull('email_verified_at')->count(),
            ],
        ]);
    }

    /**
     * Create a user from the "Add user" modal.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        return back()->with('status', __(':name was added.', ['name' => $user->name]));
    }

    /**
     * Update a user from the "Edit user" modal.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('status', __(':name was updated.', ['name' => $user->name]));
    }

    /**
     * Delete a single user.
     */
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return back()->with('status', __(':name was deleted.', ['name' => $user->name]));
    }

    /**
     * Delete the selected users. The signed-in user is always skipped.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'max:500'],
            'ids.*' => ['integer'],
        ]);

        $users = User::whereKey($validated['ids'])->get()
            ->filter(fn (User $user): bool => $request->user()->can('delete', $user));

        User::whereKey($users->modelKeys())->delete();

        return back()->with('status', trans_choice(':count user was deleted.|:count users were deleted.', $users->count()));
    }

    /**
     * Download users as CSV: the selected ids, or everything matching the current filters.
     * Rows are streamed in id order so memory stays flat on large tables.
     */
    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('viewAny', User::class);

        $request->validate([
            'ids' => ['sometimes', 'array', 'max:5000'],
            'ids.*' => ['integer'],
        ]);

        $filters = $this->filters($request);

        $query = $request->filled('ids')
            ? User::whereKey($request->input('ids'))
            : $this->filteredQuery($filters);

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Email', 'Email verified', 'Joined']);

            $query->lazyById(500)->each(function (User $user) use ($handle): void {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->email_verified_at ? 'yes' : 'no',
                    $user->created_at?->toDateTimeString(),
                ]);
            });

            fclose($handle);
        }, 'users-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Normalise the table's query-string filters against allow-lists.
     *
     * @return array{q: string, status: ?string, sort: string, direction: string}
     */
    private function filters(Request $request): array
    {
        $status = $request->string('status')->toString();
        $sort = $request->string('sort')->toString();

        return [
            'q' => $request->string('q')->trim()->toString(),
            'status' => in_array($status, ['verified', 'unverified'], true) ? $status : null,
            'sort' => in_array($sort, self::SORTABLE, true) ? $sort : 'created_at',
            'direction' => $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc',
        ];
    }

    /**
     * Apply search and status filters.
     *
     * @param  array{q: string, status: ?string, sort: string, direction: string}  $filters
     * @return Builder<User>
     */
    private function filteredQuery(array $filters): Builder
    {
        return User::query()
            ->when($filters['q'] !== '', function (Builder $query) use ($filters): void {
                $query->where(function (Builder $query) use ($filters): void {
                    $query->where('name', 'like', "%{$filters['q']}%")
                        ->orWhere('email', 'like', "%{$filters['q']}%");
                });
            })
            ->when($filters['status'] === 'verified', fn (Builder $query) => $query->whereNotNull('email_verified_at'))
            ->when($filters['status'] === 'unverified', fn (Builder $query) => $query->whereNull('email_verified_at'));
    }
}
