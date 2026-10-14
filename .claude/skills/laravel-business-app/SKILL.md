---
name: laravel-business-app
description: "Build or extend modules of a Laravel business application (ERP, back-office, admin, CRUD-heavy app) the way erp-douira does it: one page per module with a live list, side panels for create/edit/read, cards instead of horizontal scroll, folded stats, actionable alerts, prerequisites instead of empty forms, trash with restore, e-mail notifications with PDF, Arabic/RTL + French + English. Use whenever creating a new module or CRUD, a list/table, a form, a side panel, a Blade component, a vanilla-JS data-* behaviour, a notification or a PDF document, or when reviewing UI/UX, RTL or code organisation in such an app. Triggers: module, CRUD, liste, tableau, side panel, panneau, fiche, formulaire, UX, UI, RTL, arabe, mobile, cartes, notification, PDF, corbeille."
license: MIT
metadata:
  author: erp-douira
  reference-implementation: "erp-douira (Laravel 13, PHP 8.3, Blade + Tailwind 4 + vanilla JS, MySQL)"
---

# Laravel business app — modules, side panels, UX/UI

Everything here was built, broken and fixed in **erp-douira** (a sewing-factory ERP:
materials → purchases → stock → production → sales → HR/payroll). Each rule exists because
its absence caused a real bug or a real complaint. The paths quoted are that app's; in
another app, look for the equivalent or create it the same way.

## Consistency first

Before applying a rule, look at what the application already does (sibling module,
component, test). A second way of doing the same thing is worse than an imperfect first
one. These rules are the default when nothing exists yet, and the target when refactoring.

## The ten rules that hold everything together

1. **Route → controller → service → response.** The controller validates (Form Request),
   authorizes (policy / `Gate::authorize`), delegates, responds. All business logic,
   transactions and writes live in `app/Services/<Domain>/`. → `rules/architecture.md`
2. **One module = one page.** `GET /things` renders the page; the same URL with header
   `X-Partial` renders only the list fragment (search, sort, paginate without reload).
   → `rules/module-pages.md`
3. **Create, edit and read happen in a side panel**, never on a new page — and every panel
   has a full-page twin that includes **the same partial** (`fields`, `show`), so they
   cannot drift. Tests enforce the parity. → `rules/side-panels.md`
4. **A list is for deciding, a record is for reading.** Columns answer "what must I do?",
   at most three row actions, the row opens the record, the pencil opens the form, every
   counter is an SQL aggregate. → `rules/lists-and-tables.md`
5. **No horizontal scroll, ever.** A table that does not fit its frame turns each row into a
   card (`is-stacked`), whatever the screen width. → `rules/lists-and-tables.md`
6. **A form never opens empty or useless.** Missing reference data → a "prerequisites"
   panel with one button per step that comes back to the form. Only the main field is
   required. Every non-obvious field has `:help` or `:hint`. → `rules/forms.md`
7. **An alert says what to do** — finding, consequence, "What to do:" with the exact button
   name, and the button itself when it fits. → `rules/forms.md`
8. **Design tokens only, components only.** Anonymous Blade components (`<x-ui.*>`),
   semantic colour tokens (`bg-surface`, `text-ink2`, tones `run/stop/maint/brand/setup`),
   vanilla JS modules wired by `data-*` attributes. No hex, no `gray-500`, no inline
   `onclick`, no JS framework. → `rules/design-system.md`
9. **Three languages, RTL first.** Every string through `__()` with the key in all three
   `lang/*` files; logical utilities (`ps-`, `ms-`, `text-start`); numbers in `.num`;
   verify Arabic display by measuring, not by guessing. → `rules/i18n-rtl.md`
10. **Side effects never break a save.** E-mails/PDFs are sent after the response
    (`defer()`), failures are `report()`ed, nothing is rolled back.
    → `rules/notifications-and-documents.md`

## Workflow: adding a module

1. **Design first** for anything beyond a plain CRUD: tables, relations, statuses, who can
   do what. Present it and get approval before coding.
2. **Plain CRUD → generator** (`php artisan make:crud Thing --fields=… --relations=… --model`)
   if the app has one; then reshape into a module page (step 3). Business modules →
   hand-written service.
3. Build the files listed in `rules/module-pages.md` (routes in the right order, controller
   with `HandlesModulePages`, service, policy, requests, `index` + `partials/{list,fields,
   form,panel,show,view-panel}` + `create/edit/show` pages, `lang/{ar,fr,en}/things.php`).
4. Menu entry (`config/navigation.php`, with its `can`), command-palette entry
   (`config/actions.php`) if the module has a "new" action.
5. Prerequisites (`PrerequisiteService::forThing()`) if the form needs reference data.
6. Soft deletes → register the type in the trash (`TrashService::TYPES`), relations of
   historical records use `->withTrashed()`, unique rules do NOT use `withoutTrashed()`.
7. Demo data: add the module to the demo seeder (through the services, never raw inserts).
8. Tests (`rules/testing-and-verification.md`), including the **enumerating tests** that
   list every module (permissions, panels, parity, translations).
9. Verify in a real browser at 390 px, 768 px and 1300 px, in Arabic and in French.
10. Finish: `vendor/bin/pint --format agent`, `npm run build`, `php artisan test --compact`,
    journal entry in `docs/99-Journal.md`, update `docs/10-Endpoints.md` if routes changed.

## Rule index — read the files that match the task

| File | Read when |
|---|---|
| `rules/architecture.md` | controllers, services, policies, requests, traits, soft deletes, trash, helpers, error handling |
| `rules/module-pages.md` | a new module, its routes, its views and partials, the full-page fallbacks |
| `rules/side-panels.md` | anything opened in the side panel: forms, records, tabs, panel tables, footers, events |
| `rules/lists-and-tables.md` | list columns, row actions, tones, live list, stats, mobile cards, export/print, bulk |
| `rules/forms.md` | fields, required/optional, help/hint, repeaters and keyboard entry, prerequisites, alerts, defaults |
| `rules/design-system.md` | Blade components, tokens, dark mode, icons, JS modules and `data-*` wiring |
| `rules/i18n-rtl.md` | translations, RTL layout, numbers/units/signs in Arabic, Arabic in PDFs |
| `rules/notifications-and-documents.md` | e-mails (SMTP, Gmail, cPanel), activity notifications, PDF documents |
| `rules/testing-and-verification.md` | tests to write, enumerating tests to update, browser checks, finishing checklist |
