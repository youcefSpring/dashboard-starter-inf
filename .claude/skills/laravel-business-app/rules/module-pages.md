# Module pages — anatomy of a module

Reference: the suppliers module (`SupplierController`, `SupplierService`,
`resources/views/suppliers/`). Copy its shape.

## Routes (order matters)

```php
// routes/web.php, inside the auth group — specific routes BEFORE Route::resource
Route::delete('/things/bulk', [ThingController::class, 'bulkDestroy'])->name('things.bulk-destroy');
Route::get('/things/panel/{thing?}', [ThingController::class, 'panel'])->name('things.panel');   // form fragment (create / edit)
Route::get('/things/{thing}/panel', [ThingController::class, 'fiche'])->name('things.fiche');    // read-only record fragment
Route::resource('things', ThingController::class);                                                // full-page fallbacks
```

`things.panel` without id = create form, with id = edit form. `things.fiche` = read view.

## Controller actions

| Action | Returns |
|---|---|
| `index` | `listView()` → page `things.index` or fragment `things.partials.list` (+ CSV when `?export=csv`) |
| `panel(?Thing)` | `things.partials.panel` (authorize `create` or `update`) |
| `fiche(Thing)` | `things.partials.view-panel` |
| `create/edit/show` | full pages: same partials inside the app layout (no-JS fallback, print, shareable URL) |
| `store/update/destroy/bulkDestroy` | `saved()` — JSON for the panel, redirect otherwise |

`store` returns `['id' => …, 'name' => …]` in the payload: quick-create widgets (create a
supplier from inside a purchase form) select the new record immediately.

## Views

```
resources/views/things/
  index.blade.php              page: header, flash, stats, panel(title + count + search, live list)
  create.blade.php             full page: <form> + @include('things.partials.form', ['thing' => null])
  edit.blade.php               full page: same, with $thing
  show.blade.php               full page: header with actions + @include('things.partials.show')
  partials/
    list.blade.php             the fragment reloaded by live-list.js (mirrors, bulk bar, table, pagination)
    fields.blade.php           THE fields, shared by panel.blade.php and form.blade.php
    form.blade.php             full-page form card wrapping fields
    panel.blade.php            side-panel form: <form data-panel-form> + panel-shell + fields + footer
    show.blade.php             THE record body, shared by view-panel.blade.php and show.blade.php
    view-panel.blade.php       side-panel record: panel-shell + show + footer (full page, delete, edit)
```

**The parity rule**: panel and page include the same `fields` / `show` partial. They cannot
diverge, and `FormParityTest` / `ShowPanelTest` fail if someone splits them.

## index.blade.php skeleton

```blade
{{-- Module Things — one page. Search/sort/pagination reload partials.list (live-list.js);
     create/edit in the side panel (panel-crud.js). Without JS, create/edit/show pages work. --}}
<x-layouts.app :title="__('things.title')">
    <x-shell.page-header :title="__('things.title')" :subtitle="__('things.subtitle')"
        :breadcrumbs="[__('things.title') => null]">
        <x-slot:actions>
            <x-ui.export-menu />
            @can('create', App\Models\Thing::class)
                <x-ui.button variant="primary" icon="plus" :href="route('things.create')"
                    :data-panel-open="route('things.panel')">{{ __('things.new') }}</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-shell.page-header>

    <x-ui.flash />

    <x-ui.stats>   {{-- folded by default, open/closed state remembered for all lists --}}
        <x-ui.panel><x-ui.kpi :label="__('things.stats.total')" mirror="things.total" :value="$stats['total']" /></x-ui.panel>
        <x-ui.panel><x-ui.kpi :label="__('things.stats.active')" mirror="things.active" :value="$stats['active']" value-tone="run" /></x-ui.panel>
    </x-ui.stats>

    <x-ui.panel class="overflow-hidden">
        <div class="flex flex-wrap items-center gap-2 border-b border-line px-4 py-2.5">
            <h2 class="text-[13.5px] font-bold">{{ __('things.list') }}</h2>
            <x-ui.badge tone="muted"><span data-live-target="things.count">{{ $things->total() }}</span></x-ui.badge>
            <x-ui.search-bar :placeholder="__('things.search')" live class="ms-auto" />  {{-- OUTSIDE the fragment: keeps focus --}}
        </div>
        <div data-live-list class="transition-opacity aria-busy:opacity-50">
            @include('things.partials.list')
        </div>
    </x-ui.panel>
</x-layouts.app>
```

## partials/list.blade.php skeleton

```blade
<div hidden>   {{-- values recomputed with the list: header count and stats follow the filters --}}
    <span data-live-mirror="things.count">{{ $things->total() }}</span>
    <span data-live-mirror="things.total">{{ $stats['total'] }}</span>
</div>

<x-ui.bulk-bar :action="route('things.bulk-destroy')" method="DELETE">
    <x-ui.button type="submit" variant="danger" size="sm" icon="trash" data-confirm data-confirm-form>{{ __('ui.actions.delete') }}</x-ui.button>
</x-ui.bulk-bar>

@if ($things->isEmpty())
    <x-ui.empty-state :title="__('things.empty_title')" :body="__('things.empty_body')" />
@else
    <x-ui.table min-width="760px">
        <x-slot:head>
            <x-ui.table.select-all />
            <x-ui.table.th sort="name">{{ __('things.fields.name') }}</x-ui.table.th>
            <x-ui.table.th align="end">{{ __('things.fields.amount') }}</x-ui.table.th>
            <x-ui.table.th :phone="false">{{ __('things.fields.reference') }}</x-ui.table.th>
            <x-ui.table.th></x-ui.table.th>
        </x-slot:head>
        @foreach ($things as $thing)
            <tr class="cursor-pointer hover:bg-raised" data-panel-open="{{ route('things.fiche', $thing) }}" @if (! $thing->active) data-tone="muted" @endif>
                <x-ui.table.select :value="$thing->id" :label="$thing->name" />
                <x-ui.table.td><span class="font-semibold">{{ $thing->name }}</span></x-ui.table.td>
                <x-ui.table.td align="end" num>{{ money($thing->amount) }}</x-ui.table.td>
                <x-ui.table.td muted>{{ $thing->reference }}</x-ui.table.td>
                <x-ui.table.actions :delete="Gate::allows('delete', $thing) ? route('things.destroy', $thing) : null" :confirm="__('things.confirm_delete')">
                    <x-ui.history-button :subject="$thing" />
                    @can('update', $thing)
                        <x-ui.table.action icon="edit" tone="maint" :tip="__('ui.actions.edit')" :href="route('things.edit', $thing)" :panel="route('things.panel', $thing)" />
                    @endcan
                </x-ui.table.actions>
            </tr>
        @endforeach
    </x-ui.table>
@endif

<x-ui.pagination :paginator="$things" :per-page-options="$perPageOptions" />
```

## Tabs in a module page

Several related lists in one module (stock: products / raw / aids / movements; materials:
raw / aid) → tabs as links (`?tab=raw`, `role="tablist"`, `overflow-x-auto` on the tab
strip only), each tab its own query and fragment, the tab validated with `Rule::in(...)`.
When tabs depend on permissions (HR, Administration), use `HandlesTabbedPage`: `TABS` +
`tabModels()`, and **a requested but refused tab answers 403** — never a silent fallback
to another tab, or the user believes they saw the page they asked for.
Never mix two families in one list (raw materials and production aids are separate tabs).

## Navigation

```php
// config/navigation.php
['label' => 'ui.nav.short.things', 'icon' => 'box', 'route' => 'things.index', 'can' => ['viewAny', App\Models\Thing::class]],
```

Dev tools (UI kit, generator) stay out of the menu: comment the entry, keep the route.
