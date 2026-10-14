# Architecture and code organisation

## Request flow

```
routes/web.php ──► Controller ──► Service ──► Response
                   validate        all business logic,      saved() / failed()
                   authorize       transactions, writes     view / fragment / JSON
                   delegate
```

- **Controller**: Form Request (validation + `authorize()`), or `Gate::authorize()` — never
  both for the same action. Calls one service method. Returns through `saved()`, `failed()`,
  `listView()`. No query in a view, no business rule in a controller.
- **Service** (`app/Services/<Domain>/<Thing>Service.php`): create/update/delete, every
  transaction, every rule. A thin service is fine: the first rule that appears has an
  obvious home instead of settling in the controller. Example: `SupplierService::create()`
  is one line — on purpose.
- **Beginner-friendly header comments**: each controller and service starts with a PHPDoc
  that says what the module does and the path of a request
  (`route → ThingController → ThingService → response`). Comments explain *why*.
- Side effects (mail, PDF, notifications) are called **from the controller after the
  service**, so seeders and console commands that call services send nothing.

## Folders

```
app/Enums/                      closed values (UserRole, SaleStatus…) with label()/options()
app/Http/Controllers/Concerns/  HandlesModulePages, DeletesSelection, ExportsLists,
                                GuardsPrerequisites, CustomizesPdf, HandlesTabbedPage
app/Http/Requests/              Store/Update requests (or one shared request)
app/Models/Concerns/            HasStock, HasPackaging… (behaviour shared by models)
app/Policies/                   one per resource (or a shared CatalogPolicy)
app/Services/<Domain>/          business logic (Catalog, Purchases, Sales, Stock, Trash,
                                Documents, Notifications, Setup…)
app/Support/                    helpers.php (money, bidi, quantity_html), Pdf, Audit,
                                DatabaseRefusal, InlineCreate…
config/navigation.php           the menu (groups, icons, routes, `can`)
config/actions.php              the command palette (Ctrl+K)
```

Do not create new top-level folders without approval.

## Controller traits (the shared contract)

`HandlesModulePages` — every module controller uses it:

| Method | Does |
|---|---|
| `wantsFragment($request)` | header `X-Partial` present → return the fragment only |
| `filters($request, $own, sortable:, searchable:)` | validates `q`, `sort` (allow-list `SORTABLE`), `dir`, `per_page` (`PER_PAGE`) + the module's own filters. `?sort=` never reaches `orderBy()` raw |
| `listView($request, $page, $fragment, $data)` | page, fragment, `?export=print` (same fragment in a print layout) or `?export=pdf` |
| `perPage($request, self::PER_PAGE)` | allowed page size; on print/export takes everything (limit 2000) |
| `saved($request, $message, $redirect, $payload)` | JSON `{message, …payload, redirect}` for panels/AJAX, redirect + flash `success` otherwise (`download` URL travels in session) |
| `failed($request, $message, $redirect, $status)` | refusal: JSON 4xx for panels, redirect + flash `error` otherwise — an action launched from a list has no field to carry an error |
| `shortage(...)` | business refusal turned into a field error (422) in a panel, banner on a full page |

Others: `DeletesSelection::deleteSelected()` (bulk delete, each row through its policy),
`ExportsLists::streamCsv()` (CSV streamed), `GuardsPrerequisites` (see forms.md),
`CustomizesPdf` (admin retouches PDF texts before download).

Constants on each controller: `SORTABLE`, `SEARCHABLE`, `PER_PAGE = [10, 25, 50]`.

## Authorization

- Policies with `viewAny/view/create/update/delete` (+ `confirm`, `cancel`… when needed).
- Fine permissions (`sales.view`, `sales.create`…) distributed from a roles page when roles
  differ per module; simple role checks (`User::isAdmin()`, `managesOperations()`) otherwise.
- Gates for app-wide screens (`manage-trash`, `use-generator`).
- Menu entries and buttons use the same ability (`'can' => ['viewAny', Thing::class]`,
  `@can('create', Thing::class)`) — a visible button the user cannot use is a bug.

## Models

- `#[Fillable([...])]` explicit, casts in `casts()`, enums cast to enum classes.
- Money/quantities as `decimal` columns; display via helpers, never `number_format` ad hoc.
- **Soft deletes** on every business record that history refers to. Then:
  - historical relations use `->withTrashed()` (`Sale::customer`, `PurchaseItem::variant`,
    `Payroll::employee`…): *an invoice that no longer says who bought is worthless*;
  - still dereference with `?->` and fall back to `__('ui.deleted_record')`;
  - a parent deleted → its children deleted in the same event (`static::deleted` in
    `booted()`), and counters filter `whereHas('parent', fn ($q) => $q->withoutTrashed())`;
  - **unique rules must NOT use `->withoutTrashed()`**: the database unique index still
    holds the deleted row's name → a 500 when re-creating it. Validate against all rows and
    let the message point to the trash (`validation.unique` mentions it).
- Aggregates on boolean columns: `SUM(active)` through Eloquent goes through the boolean
  cast (always 1) — use `->toBase()->first()` for stats queries.

## Trash (restore / permanent delete)

`TrashService::TYPES` maps a type key to its model and its unique columns. Rules:
- restoring a parent restores the children deleted **at the same time** (`deleted_at >=`),
  not those deleted earlier one by one;
- a child whose parent is in the trash is hidden from its tab and cannot be restored alone;
- restore refused when an active record took the same name;
- permanent delete refused when the record has history (stock movements, attendances,
  payrolls…) or when the database refuses (SQLSTATE 23000 caught) — it may stay in the
  trash forever. Admin only (gate).

## Errors

- `App\Support\DatabaseRefusal` turns 1062 (duplicate) and 1451 (referenced row) into a
  business message and logs the technical detail. Any other SQL error bubbles up: hiding a
  real defect costs more than the error page.
- Domain exceptions (`InsufficientStockException`) carry the data the UI needs
  (`shortages`) and are converted by the controller (`shortage()`).

## Single writers

When a quantity must stay consistent (stock), **one service method writes it**
(`StockService::move()`), everything else reads. Documents (purchase, sale, production)
call it; cancellation writes reverse movements, never edits history.

## Helpers

- `money($value, $decimals = 2)` — thousands with a thin no-break space (U+202F).
- `$unit->format($qty)` / `$model->formatQuantity($qty)` — value + U+00A0 + unit symbol.
- `quantity_html($text)` — for HTML only: isolates the signed number in `<bdi dir="ltr">`.
- `bidi($text)` — wraps a Latin fragment in FSI/PDI inside an Arabic sentence (attributes,
  plain text where markup is impossible).
