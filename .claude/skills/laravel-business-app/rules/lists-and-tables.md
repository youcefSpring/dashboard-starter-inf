# Lists and tables

## A list is for deciding

- **Columns answer "what must I do?"**, not "what is in the record?". E-mail, tax number,
  creation date, internal counters belong to the record. A list shows what decides: an
  amount, an age, a shortage.
- Show it **readably**: "3 days ago" rather than a date; a tint when a threshold is crossed;
  **names as pills** rather than a count ("Adidas · Puma · Eden Park", red pill = below
  minimum, grey = inactive); a big unit as "1 roll = 50 m" rather than "metre (metre)".
- **Every counter is an SQL aggregate** (`withCount`, `withSum`, `withMax`, subquery). A
  per-row computation makes page 2 slow.

## Row actions: three, coloured, never duplicated

- The **row** opens the record (read) in the panel; the **pencil** opens the form. No "eye"
  button: it repeated the row. The full page stays reachable from the record footer.
- At most: history, pencil, trash — plus what is specific to the module (stock movements of
  a material, the day of an employee).
- `<x-ui.table.action>` colour code, same in every list, filled pill visible at rest:

| Tone | Background | Actions |
|---|---|---|
| `neutral` | `bg-ink2` | view, history, movements (consulting changes nothing) |
| `maint` | `bg-maint` (blue) | edit, add |
| `brand` | `bg-brand` (amber) | the day/attendance of a person |
| `stop` | `bg-stop` (red) | delete, remove a repeater row |

- Passing `:edit` to `<x-ui.table.actions>` AND a pencil in the slot makes two pencils.
- Destructive actions always confirm: `data-confirm` + `data-confirm-action` +
  `data-confirm-method="DELETE"`; the shared modal posts by itself.

## Row tone

`data-tone="run|stop|warn|watch|setup|maint|brand|neutral|muted"` on the `<tr>` tints the
row and draws a start bar; it wins over zebra and hover. Compute it in the model
(`Material::stockTone()` = the most endangered variant) and add a legend
(`<x-ui.row-legend preset="catalog" />`).

## Live list

- `<x-ui.search-bar live />` (debounce 300 ms) and sort/pagination links (`data-live-link`)
  reload `[data-live-list]` with header `X-Partial`. URLs stay clean and shareable.
- The search field lives **outside** the fragment (focus kept while typing).
- Values outside the fragment follow through `data-live-mirror` → `data-live-target` /
  `<x-ui.kpi mirror="…">`.
- Over 180 ms the `tbody` shows skeleton rows automatically — no view to change.
- Filters on selects: `data-autosubmit`.

## Stats: folded

Counters above a list are wrapped in `<x-ui.stats>`: folded by default (button
"الإحصائيات"), open/closed remembered for all lists in `localStorage` (`ui.stats.open`,
wrapped in try/catch). The list is the work; stats are a glance on demand.

## No horizontal scroll — rows become cards

- `<x-ui.table min-width="760px">` sets the width the table needs.
- `resources/js/ui/table.js::fitStack()` adds `is-stacked` when the frame is narrower than
  that min width **or** than the width the content actually claimed, and always under
  640 px. A `ResizeObserver` follows each frame (rotation, panel opening). CSS
  (`table.table-stack.is-stacked`) turns each row into a card: first cell = card title,
  other cells "column title … value" (labels copied from the header into `data-label`),
  checkbox in the corner, actions as a footer, row tone on the card edge.
- `<x-ui.table.th :phone="false">` hides a secondary column from the cards (unit, reference).
- `:stack="false"` keeps a real table — only for data-entry grids read in columns that fit.
- The frame scrolls only horizontally (`overflow-y: hidden`), tooltips of the body open
  upwards (a hidden tooltip below the last row created a vertical scrollbar).
- Verify at 390, 640, 768, 900, 1024 and 1280 px (testing-and-verification.md).

## Export and print

`<x-ui.export-menu />` → CSV (`ExportsLists::streamCsv`, streamed, translated headers) and
print/PDF, which reuse **the same list fragment** (`listView()` with `?export=print|pdf`):
what you see is what prints, no second template.

## Bulk actions

`<x-ui.table.select-all />`, `<x-ui.table.select :value :label />` and
`<x-ui.bulk-bar :action method="DELETE">` (visible when rows are checked, posts `ids[]`).
Server: `deleteSelected()` re-checks **each** row against its policy.

## Pagination

`<x-ui.pagination :paginator :per-page-options="[10, 25, 50]" />`; queries always
`->withQueryString()`; filters validated by `filters()`.
