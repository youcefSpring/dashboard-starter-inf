# Forms, prerequisites and alerts

## Fields

- **Only the main attribute(s) are required.** Everything else nullable. A form that
  demands ten fields is a form people avoid.
- Two fields per row in panels (`<x-ui.fieldset cols="2">`); long text spans both.
- Components read `old()` and `$errors` themselves: `<x-ui.input name="name" :label
  :value="$thing?->name" required autofocus />`. `$thing` is null on create — the same
  `fields` partial serves create and edit.
- `<x-ui.select>` is **searchable by default** (combobox, keyboard); `:searchable="false"`
  for a native select. Options are re-read on each use → can be filled by AJAX
  (`setComboboxOptions`) for cascades (material → variants). An option with `data-disabled`
  stays visible but greyed (already used by another line).
- `create="unit"` on a select offers "+ add «typed text»" (inline create through
  `App\Support\InlineCreate`): never send the user away to create a missing unit.
- Numbers: `<x-ui.input type="number">` carries `inputmode="decimal"` (tablet keypad).
- Quantities can be typed in the big unit or the base unit; the server converts
  (`HasPackaging::quantityInBase()`) and snapshots `pack_size` on the line.

## Defaults that save typing

- A new record copies sensible values from the **previous one of the same family**
  (unit, big unit and content of the last material of that kind) — a `template` prop on the
  shared field component, never stored as hidden magic.
- Product → variant rows prefilled from the product; sale lines propose the product price,
  editable.
- Server-assigned references (`VTE-2026-0008`) are read-only and never typed; a typed one is
  ignored.

## Explaining a field: `:help` or `:hint`, one at a time

| | Rendering | When |
|---|---|---|
| `:help` | a "?" next to the label, tooltip on hover **and keyboard focus** | the normal case — one sentence that does not lengthen the form |
| `:hint` | grey line under the field, always visible | a warning nobody may miss ("the code can't change after creation") |

- The "?" is a `<button>`, never inside a `<label>` (it would steal the click), with
  `aria-label`; tooltips wrap with `data-tip-wrap`.
- Repeater rows have no label: put `<x-ui.help>` on the **column header**.
- **An undeclared prop becomes an inert HTML attribute and disappears silently** — declare
  every prop in `@props`, and test the rendering (`FormFieldHelpTest`).

## Repeaters and keyboard entry

```blade
<div data-repeater data-repeater-name="variants" data-repeater-min="0" data-repeater-auto data-repeater-enter="row">
```

- `__INDEX__` in the row template is replaced; events `repeater:add` / `repeater:remove`
  (`detail.row`) let widgets initialise in new rows.
- **Enter goes to the next field**; on the last field it adds a row. Typing a document
  happens on the keyboard, without reaching for "Add" with the mouse.
- `data-repeater-auto`: there is always one empty row at the bottom; empty rows are ignored
  on save. `data-repeater-enter="row"`: Enter jumps to the next row whatever the field.
- The "+" button stays: if the last row is empty it just focuses it.
- Two short items per row when the row is just a name (variant names).

## Prerequisites instead of an empty form

A form that cannot serve yet (no unit, no product with a bill of materials, no employee…)
does not open empty: it says what to do, in order, one button per step.

```php
// Controller (GuardsPrerequisites), ALWAYS after Gate::authorize():
if ($missing = $this->missingPanel('production', $this->prerequisites()->forProduction(), route('productions.panel'))) {
    return $missing;   // side panel: the setup panel instead of the form
}
if ($missing = $this->missingRedirect('production', $this->prerequisites()->forProduction(), route('productions.index'), route('productions.panel'))) {
    return $missing;   // full page: back to the list, modal opened automatically
}
```

- What is missing is known in ONE place: `PrerequisiteService::forXxx()` returning
  `Requirement` items; texts in `lang/*/setup.php` (`items.<key>.title|body|action`).
- `<x-ui.prerequisites :items :return>`: numbered cards; the fix button carries
  `data-panel-return` → after creating the unit, back to the material form.
- A fix button appears only if the user may use the target page (`canFix`); otherwise
  "ask an administrator".
- Non-blocking advice at the top of a form: `<x-ui.prerequisites :items compact />`.
- Dashboard: a "getting started" checklist, invisible once everything is done.

## An alert says WHAT TO DO

A finding is not a message. "Piece rates missing" tells there is a problem, not how to get
out of it. Every alert in a panel carries:

1. **the finding** — short, in `:title`;
2. **the consequence** — why it matters ("their payslip will be 0 and nothing will warn you");
3. **"What to do:"** — the steps, with the **exact name of the button** to click.

When the action fits on this screen, the alert carries the button (`actions` slot): the
best instruction is the one you don't have to follow by hand. A list pill label is not an
alert body: keep a short `state` key for the pill and a full `state_help` for the alert
(tooltip on the pill). `PanelAlertsAreActionableTest` requires "What to do" in all languages.

## Validation messages

- Form Requests translate attributes (`attributes()` → `__('things.fields.name')`).
- `unique` against soft-deleted rows: the message says the name may belong to a record in
  the trash.
- Errors of an action launched from a list → flash banner (`failed()`), never a field error
  on a field that is not on screen.
