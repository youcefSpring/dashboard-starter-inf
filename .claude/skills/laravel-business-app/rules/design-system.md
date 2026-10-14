# Design system — components, tokens, JavaScript

## Stack

Blade anonymous components + Tailwind CSS 4 (CSS-first config in `resources/css/app.css`,
no `tailwind.config.js`) + Vite + vanilla ES modules + Chart.js. Fonts served locally
(`public/fonts/`), no CDN. jQuery only if already present for AJAX-heavy forms.

## Tokens and colours

- Colours are RGB variables on `:root` (`--surface`, `--raised`, `--line`, `--ink`,
  `--ink2`, `--ink3`, `--brand`…), redefined under `.dark` on `<html>`, exposed by
  `@theme inline` → `bg-surface`, `text-ink2`, `border-line`, `bg-brand/15`.
- **Semantic tones**, the same everywhere: `run` (green, ok/active), `stop` (red,
  danger/below minimum/delete), `maint` (blue, edit/add), `brand` (amber, primary/person),
  `setup` (violet), `warn`, `watch`, `neutral`, `muted` — each with a `-soft` variant.
- **No hex, no `gray-500`, no `text-red-600` in views.** A new tone composed at runtime
  (`bg-{{ $tone }}-soft`) must be listed in an `@source inline(...)` of `app.css`, or Tailwind
  never generates it. Prefer full class names in a PHP array (see `<x-ui.table.action>`).
- Dark mode: `resources/js/ui/theme.js` toggles `.dark`, stores `localStorage.theme`, emits
  `theme:change` (charts redraw); an anti-flash script in `<head>`.
- Radii and sizes as tokens (`rounded-panel`, `rounded-ctl`); dense type (12.5–13.5 px in
  lists) — this is a work tool, not a landing page.

## Components

- One component per file in `resources/views/components/ui/`, **usage comment at the top**
  (example calls + why it exists), `@props` with defaults, `{{ $attributes->class([...]) }}`
  so the caller can add classes.
- Prefer an existing component; look at the catalogue (`docs/02-Composants-UI.md`, `/ui-kit`).
  New component → documented there + shown on `/ui-kit`.
- Families: buttons/badges (`button variant=primary|secondary|ghost|danger|danger-soft|icon`,
  `badge tone`), containers (`panel`, `card`, `kpi`, `stats`, `empty-state`, `tabs`,
  `description-list`), feedback (`flash`, `alert`, toasts via `data-toast`, tooltips via
  `data-tip`), overlays (`modal`, `drawer`, side panel, shared `confirm-modal`), forms
  (`input`, `select`, `multi-select`, `toggle`, `number-input`, `date-input`, `file-input`,
  `repeater`, `fieldset`, `form-card`), lists (`search-bar`, `bulk-bar`, `table.*`,
  `pagination`, `export-menu`), `chart`, `lazy` + `skeleton`, shell (`page-header`,
  `topbar`, `main-nav`).
- Icons: `<x-ui.icon name="plus" class="h-4 w-4" stroke="2" />` from one SVG sprite; a new
  icon = a `<symbol>` in `icon-sprite.blade.php`.

## Blade traps

- Never nest Blade comments: `{{--` closes at the FIRST `--}}` and the rest leaks into HTML.
- Never name a variable `$component` (reserved).
- Conditional classes inside a component tag: `:class="Arr::toCssClasses([...])"`, not `@class`.
- JSON for JS: `<script type="application/json">{!! json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}</script>`,
  not a multi-line `@json`.
- Pass every value the view needs from the controller; no queries in views.

## JavaScript

- One behaviour = one module `resources/js/ui/<name>.js` exporting `initXxx()`, called in
  `resources/js/app.js`. Business screens get their own folder (`resources/js/products/`).
- Triggered by `data-*` attributes, never inline `onclick`. Delegated listeners on
  `document` for anything that may be injected later (overlay, confirm, bulk, table).
- Injected fragments are re-enhanced with `enhance(root)`; a `MutationObserver` +
  `requestAnimationFrame` handles content added by other modules (table cards).
- Colours in JS (SVG, charts) read CSS variables (`rgb('--brand')`) and redraw on
  `theme:change`.
- Storage (`localStorage`) only for per-user conveniences (theme, stats folded), always in
  try/catch; never for data.
- After a change: `npm run build` (or `npm run dev`; a `public/hot` file means the dev server
  is expected — production tests may then fail on CSP nonce).

## Page layout

```blade
<x-layouts.app :title>          base html, topbar, main nav, toasts, confirm modal, side panel host
  <x-shell.page-header :title :subtitle :breadcrumbs> <x-slot:actions>…</x-slot:actions>
  <x-ui.flash />
  …content (max width 1440 px)…
</x-layouts.app>
```

Mobile: 16 px gutters, no horizontal page scroll, tab strips may scroll horizontally on
their own (`role="tablist"`), tables become cards (lists-and-tables.md).
