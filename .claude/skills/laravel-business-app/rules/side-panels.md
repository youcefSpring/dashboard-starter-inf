# Side panels

The side panel is the main working surface: create, edit and read without leaving the
list. Implementation: host `<x-ui.side-panel>` placed **once** in the app layout,
`resources/js/ui/panel-crud.js`, fragment wrapper `<x-ui.panel-shell>`.

## Opening

```blade
<a href="{{ route('things.create') }}" data-panel-open="{{ route('things.panel') }}">New</a>
```

- `href` = the no-JS fallback (full page). `data-panel-open` = the fragment URL.
- Works from anywhere: list rows, buttons, links inside another panel, the command palette
  (`openPanel(url)`), and it falls back to navigation when no host exists.
- **Never `data-row-href` and `data-panel-open` on the same row**: both listeners sit on the
  document; the panel would open AND the page would change.

## The fragment

```blade
{{-- FORM — GET /things/panel/{thing?} --}}
<form method="POST" action="{{ $thing ? route('things.update', $thing) : route('things.store') }}"
      class="contents" novalidate data-panel-form>
    @csrf
    @if ($thing) @method('PUT') @endif
    <x-ui.panel-shell :title="$thing ? __('things.edit') : __('things.new')" :subtitle="$thing?->name" size="md">
        @include('things.partials.fields')
        <x-slot:footer>
            @if ($thing)
                <x-ui.button variant="ghost" size="sm" :href="route('things.show', $thing)" class="me-auto">{{ __('ui.actions.full_page') }}</x-ui.button>
            @endif
            <x-ui.button variant="secondary" data-close>{{ __('ui.actions.cancel') }}</x-ui.button>
            <x-ui.button type="submit" variant="primary">{{ $thing ? __('ui.actions.save') : __('ui.actions.create') }}</x-ui.button>
        </x-slot:footer>
    </x-ui.panel-shell>
</form>

{{-- RECORD — GET /things/{thing}/panel --}}
<x-ui.panel-shell :title="$thing->name" :subtitle="$thing->phone">
    @include('things.partials.show')
    <x-slot:footer>
        <x-ui.button variant="ghost" size="sm" icon="print" :href="route('things.show', $thing)" class="me-auto">{{ __('ui.actions.full_page') }}</x-ui.button>
        @can('delete', $thing)
            <x-ui.button variant="danger-soft" icon="trash" data-confirm :data-confirm-action="route('things.destroy', $thing)"
                data-confirm-method="DELETE" :data-confirm-body="__('things.confirm_delete')">{{ __('ui.actions.delete') }}</x-ui.button>
        @endcan
        @can('update', $thing)
            <x-ui.button variant="primary" icon="edit" :href="route('things.edit', $thing)" :data-panel-open="route('things.panel', $thing)">{{ __('ui.actions.edit') }}</x-ui.button>
        @endcan
    </x-slot:footer>
</x-ui.panel-shell>
```

- `class="contents"` on the form: the shell's header/body/footer stay direct flex children.
- **The width travels with the fragment**: `size` = `sm 520 · md 640 (default) · lg 800 ·
  xl 960 · 2xl 1200`. The content knows what it needs; the same panel opens at the right
  width from any page.
- Footer order: secondary navigation (`ghost`, `me-auto`, pushed to the start) · destructive
  (`danger-soft`) · cancel (`secondary`) · primary action last.
- Reading is the default action, editing is one click away (`Edit` in the record footer).

## Saving

- `data-panel-form` → submitted in AJAX (`Accept: application/json`).
- **2xx** → toast with `message`, panel closes, the live list refreshes, event
  `panel:saved` fires (deferred blocks `<x-ui.lazy>` reload themselves). `download` in the
  payload → the PDF downloads by itself.
- **422** → messages placed under their fields, panel stays open, input intact.
- A business refusal must come back as **4xx JSON** (`failed()`, `shortage()`): a redirect
  is read as success and the panel would close without saving.
- `Ctrl+Enter` saves the current form anywhere (even from a textarea).

## Panel → panel

- A link inside a panel with `data-panel-open` replaces the content (a purchase line opens
  the purchase, "new purchase" from a supplier record pre-selects the supplier).
- `data-panel-return="<url>"` (set by prerequisites): after the fixing panel saves, the
  original form reopens. Same-origin URLs only (`sameOrigin()`).
- `reloadPanel()` after an inner action (a line deleted inside a payroll).

## Content rules inside a panel

- **Two fields per row**: `<x-ui.fieldset cols="2">`; only long text areas take
  `class="sm:col-span-2"`.
- **Tabs** for an edit panel with related data (`info` / `purchases`), with
  `data-tabs-hash="false"` (a panel must not rewrite the page URL).
- A panel is already a frame: `<x-ui.table panel>` removes grid lines, zebra and the filled
  header, and lowers the min width to 420 px. Use a table only for truly tabular data
  (several numeric columns); one fact per entry → a `divide-y` list.
- Action pills become flat inside a panel automatically (CSS on `[data-side-panel]
  .action-pill`) — do not add a prop for it.
- **One panel can show only part of a form**: `things.panel?only=variants` hides the main
  fields (`data-material-fields hidden`) and focuses an empty variant row — "+ new variant"
  from a list opens exactly what is needed.
- Long explanations go in `:help` tooltips, not paragraphs; alerts follow forms.md.

## Server side checklist

- `panel()` authorizes `create` or `update`; `fiche()` authorizes `view`.
- Load everything the fragment shows in the controller (no queries in the partial).
- `panel()` checks prerequisites (`missingPanel(...)`) after authorizing.
- Every write action goes through `saved()` / `failed()` so it answers JSON to the panel.
