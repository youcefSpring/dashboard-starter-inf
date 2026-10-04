@php
    $statusOptions = ['verified' => __('Verified'), 'unverified' => __('Unverified')];
    $hasFilters = $filters['q'] !== '' || $filters['status'];
    $editingId = old('user_id');
@endphp

<x-layouts.app :title="__('Users')">
    <x-page-header :title="__('Users')" :description="__('Everyone with an account in this workspace.')">
        <x-slot:actions>
            <x-button :href="route('users.export', request()->only(['q', 'status', 'sort', 'direction']))" icon="download">{{ __('Export CSV') }}</x-button>
            <x-button variant="primary" icon="user-plus" data-dialog-open="user-create">{{ __('Add user') }}</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card :title="__('All users')" :subtitle="trans_choice(':count account in total|:count accounts in total', $totalUsers, ['count' => number_format($totalUsers)])" class="mt-6">
        <x-slot:actions>
            <x-table.search :value="$filters['q']" :placeholder="__('Search by name or email')" class="w-full sm:w-64" />
            <x-table.filter name="status" :label="__('Status')" :options="$statusOptions" :counts="$statusCounts" :selected="$filters['status']" />
        </x-slot:actions>

        <x-table.root id="users-table">
            <x-table.toolbar>
                <span><b class="num font-semibold text-fg">{{ number_format($users->total()) }}</b> {{ trans_choice('user|users', $users->total()) }}@if ($filters['q'] !== '') {{ __('matching') }} “{{ $filters['q'] }}”@endif</span>
                @if ($filters['status'])
                    <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => null]) }}" aria-label="{{ __('Remove filter :name', ['name' => $statusOptions[$filters['status']]]) }}"
                        class="inline-flex items-center gap-1 rounded-md bg-surface-2 px-2 py-1 text-xs font-medium text-fg-2 ring-1 ring-inset ring-line transition-colors hover:text-fg">
                        {{ $statusOptions[$filters['status']] }}<x-icon name="x" class="size-3" />
                    </a>
                @endif
                @if ($hasFilters)
                    <a href="{{ route('users.index') }}" class="rounded-md px-1.5 py-1 text-xs font-semibold text-primary-ink hover:underline">{{ __('Reset') }}</a>
                @endif

                <x-slot:bulk>
                    <form method="GET" action="{{ route('users.export') }}" data-table-bulk-form>
                        <x-button type="submit" size="sm" variant="ghost" icon="download">{{ __('Export') }}</x-button>
                    </form>
                    <x-button size="sm" variant="ghost" icon="trash-2" class="!text-error hover:!bg-error/10" data-dialog-open="users-bulk-delete">{{ __('Delete') }}</x-button>
                </x-slot:bulk>
            </x-table.toolbar>

            <x-table min-width="760px" :sort="$filters['sort']" :direction="$filters['direction']">
                <x-slot:head>
                    <x-table.select-all />
                    <x-table.th column="name">{{ __('User') }}</x-table.th>
                    <x-table.th>{{ __('Email status') }}</x-table.th>
                    <x-table.th column="created_at">{{ __('Joined') }}</x-table.th>
                    <x-table.th column="updated_at">{{ __('Last updated') }}</x-table.th>
                    <x-table.th><span class="sr-only">{{ __('Actions') }}</span></x-table.th>
                </x-slot:head>

                @forelse ($users as $user)
                    @php
                        $isSelf = $user->is(auth()->user());
                        $fill = [
                            'action' => route('users.update', $user),
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                        ];
                    @endphp
                    <tr>
                        <x-table.select :value="$user->id" :label="__('Select :name', ['name' => $user->name])" :disabled="$isSelf" />
                        <td>
                            <span class="flex items-center gap-3">
                                <x-avatar :name="$user->name" size="sm" />
                                <span class="min-w-0">
                                    <span class="flex items-center gap-2">
                                        <span class="truncate font-semibold text-fg">{{ $user->name }}</span>
                                        @if ($isSelf)
                                            <x-badge tone="primary">{{ __('You') }}</x-badge>
                                        @endif
                                    </span>
                                    <span class="block truncate text-xs text-fg-3">{{ $user->email }}</span>
                                </span>
                            </span>
                        </td>
                        <td>
                            @if ($user->email_verified_at)
                                <x-badge tone="success" dot>{{ __('Verified') }}</x-badge>
                            @else
                                <x-badge tone="warning" dot>{{ __('Unverified') }}</x-badge>
                            @endif
                        </td>
                        <td class="num whitespace-nowrap text-fg-2">{{ $user->created_at?->format('M j, Y') }}</td>
                        <td class="whitespace-nowrap text-fg-2">{{ $user->updated_at?->diffForHumans() }}</td>
                        <x-table.row-actions :label="__('Actions for :name', ['name' => $user->name])">
                            <x-dropdown-item icon="eye" data-dialog-open="user-view" :data-dialog-fill="json_encode([
                                'name' => $user->name,
                                'email' => $user->email,
                                'status' => $user->email_verified_at ? __('Verified') : __('Unverified'),
                                'joined' => $user->created_at?->format('F j, Y'),
                                'updated' => $user->updated_at?->diffForHumans(),
                            ])">{{ __('View details') }}</x-dropdown-item>
                            <x-dropdown-item icon="pencil" data-dialog-open="user-edit" :data-dialog-fill="json_encode($fill)">{{ __('Edit user') }}</x-dropdown-item>
                            <x-dropdown-item icon="copy" data-copy="{{ $user->email }}">{{ __('Copy email') }}</x-dropdown-item>
                            @unless ($isSelf)
                                <x-dropdown-separator />
                                <x-dropdown-item icon="trash-2" danger data-dialog-open="user-delete"
                                    :data-dialog-fill="json_encode(['action' => route('users.destroy', $user), 'name' => $user->name])">{{ __('Delete user') }}</x-dropdown-item>
                            @endunless
                        </x-table.row-actions>
                    </tr>
                @empty
                    <x-table.empty :colspan="6" icon="search-x" :title="__('No users found')" :text="__('Try a different name or email address, or clear the filters.')">
                        <x-button size="sm" :href="route('users.index')">{{ __('Reset filters') }}</x-button>
                    </x-table.empty>
                @endforelse
            </x-table>

            <div class="flex flex-col-reverse items-center justify-between gap-3 pt-1 sm:flex-row">
                <x-table.per-page :options="\App\Http\Controllers\UserController::PER_PAGE" :value="$perPage" />
                <div class="w-full sm:w-auto">{{ $users->links('partials.pagination') }}</div>
            </div>
        </x-table.root>
    </x-card>

    {{-- Add user --}}
    <x-modal id="user-create" :title="__('Add user')" :description="__('They can sign in straight away with this password.')" icon="user-plus"
        :data-open-on-load="$errors->createUser->isNotEmpty() ? 'true' : null">
        <form id="user-create-form" method="POST" action="{{ route('users.store') }}" class="space-y-4" data-loading-form>
            @csrf
            <x-field name="name" id="create_name" bag="createUser" :label="__('Full name')" required autocomplete="off" />
            <x-field name="email" id="create_email" type="email" bag="createUser" :label="__('Email')" icon="mail" required autocomplete="off" />
            <x-field name="password" id="create_password" type="password" bag="createUser" :label="__('Password')" icon="lock" required autocomplete="new-password" :hint="__('At least 8 characters.')" />
        </form>
        <x-slot:footer>
            <x-button data-dialog-close>{{ __('Cancel') }}</x-button>
            <x-button type="submit" form="user-create-form" variant="primary" data-loading-label="{{ __('Adding') }}">{{ __('Add user') }}</x-button>
        </x-slot:footer>
    </x-modal>

    {{-- Edit user (filled per row) --}}
    <x-modal id="user-edit" :title="__('Edit user')" icon="pencil"
        :data-open-on-load="$errors->updateUser->isNotEmpty() ? 'true' : null">
        <form id="user-edit-form" method="POST" action="{{ $editingId ? route('users.update', $editingId) : '' }}" class="space-y-4" data-loading-form>
            @csrf
            @method('PATCH')
            <input type="hidden" name="user_id" value="{{ $editingId }}" />
            <x-field name="name" id="edit_name" bag="updateUser" :label="__('Full name')" required autocomplete="off" />
            <x-field name="email" id="edit_email" type="email" bag="updateUser" :label="__('Email')" icon="mail" required autocomplete="off"
                :hint="__('Changing the email marks it as unverified.')" />
        </form>
        <x-slot:footer>
            <x-button data-dialog-close>{{ __('Cancel') }}</x-button>
            <x-button type="submit" form="user-edit-form" variant="primary" data-loading-label="{{ __('Saving') }}">{{ __('Save changes') }}</x-button>
        </x-slot:footer>
    </x-modal>

    {{-- Delete one user (filled per row) --}}
    <x-modal id="user-delete" :title="__('Delete user?')" icon="trash-2" tone="error" size="sm:max-w-md">
        <p class="text-[13px] text-fg-2">{!! __(':name will lose access immediately. This can\'t be undone.', ['name' => '<b class="font-semibold text-fg" data-fill="name"></b>']) !!}</p>
        <form id="user-delete-form" method="POST" action="" data-loading-form>
            @csrf
            @method('DELETE')
        </form>
        <x-slot:footer>
            <x-button data-dialog-close autofocus>{{ __('Cancel') }}</x-button>
            <x-button type="submit" form="user-delete-form" variant="danger" data-loading-label="{{ __('Deleting') }}">{{ __('Delete user') }}</x-button>
        </x-slot:footer>
    </x-modal>

    {{-- Bulk delete --}}
    <x-modal id="users-bulk-delete" :title="__('Delete selected users?')" icon="trash-2" tone="error" size="sm:max-w-md" data-table="users-table">
        <p class="text-[13px] text-fg-2">
            {!! __(':count selected accounts will be deleted. Your own account is never included.', ['count' => '<b class="num font-semibold text-fg" data-table-selected-count>0</b>']) !!}
        </p>
        <form id="users-bulk-delete-form" method="POST" action="{{ route('users.bulk-destroy') }}" data-table-bulk-form data-table="users-table" data-loading-form>
            @csrf
            @method('DELETE')
        </form>
        <x-slot:footer>
            <x-button data-dialog-close autofocus>{{ __('Cancel') }}</x-button>
            <x-button type="submit" form="users-bulk-delete-form" variant="danger" data-loading-label="{{ __('Deleting') }}">{{ __('Delete users') }}</x-button>
        </x-slot:footer>
    </x-modal>

    {{-- User details (filled per row) --}}
    <x-drawer id="user-view" :title="__('User details')">
        <div class="space-y-5 p-4 sm:p-5">
            <div>
                <p class="text-lg font-semibold" data-fill="name"></p>
                <p class="text-[13px] text-fg-2" data-fill="email"></p>
            </div>
            <dl class="grid grid-cols-2 gap-2">
                <div class="rounded-lg bg-surface-2 p-3"><dt class="text-xs font-medium text-fg-2">{{ __('Email status') }}</dt><dd class="mt-1 text-sm font-semibold" data-fill="status"></dd></div>
                <div class="rounded-lg bg-surface-2 p-3"><dt class="text-xs font-medium text-fg-2">{{ __('Joined') }}</dt><dd class="mt-1 text-sm font-semibold" data-fill="joined"></dd></div>
                <div class="col-span-2 rounded-lg bg-surface-2 p-3"><dt class="text-xs font-medium text-fg-2">{{ __('Last updated') }}</dt><dd class="mt-1 text-sm font-semibold" data-fill="updated"></dd></div>
            </dl>
        </div>
        <x-slot:footer>
            <x-button class="flex-1" data-dialog-close>{{ __('Close') }}</x-button>
        </x-slot:footer>
    </x-drawer>
</x-layouts.app>
