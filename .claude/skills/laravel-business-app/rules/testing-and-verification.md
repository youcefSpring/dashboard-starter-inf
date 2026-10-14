# Testing and verification

## Feature tests per module

PHPUnit feature tests, `RefreshDatabase`, dedicated test database, factories with states
(`User::factory()->role(UserRole::Admin)`). Name tests as sentences
(`test_a_draft_sends_nothing_until_it_is_confirmed`) with a class PHPDoc stating the rule.

Cover for each module:
- access per role (allowed, forbidden — including panels and bulk routes);
- index: search, filters, sort allow-list, pagination, the fragment with `X-Partial`;
- store/update validation (only main field required), panel JSON vs redirect;
- destroy + bulk destroy (each row through its policy);
- stats/counters (aggregates, booleans, soft-deleted parents excluded);
- business rules of the service (stock, totals, statuses, reversal on cancel).

## Enumerating tests — update them with every new module

These list every module on purpose; a new module that is not added is silently untested:
- `PermissionCoverageTest` (`ENTRY_POINTS`, `writes()`, `seedFixtures()`);
- `ModulePageTest` (panel list);
- `FormParityTest` (`panelPairs`, `pagePairs`) — panel and page show the same fields;
- `ShowPanelTest` (models + markers) — record panel and page show the same body;
- `FormRequiresWhatItShowsTest::screens()`;
- `TranslationCoverageTest`, `PanelAlertsAreActionableTest`, `FormFieldHelpTest`;
- `DeletedRecordSafetyTest` when adding soft deletes or historical relations;
- the demo seeder test's screen list.

Forms that need reference data: start from a helper that seeds a ready catalogue
(`seedReadyCatalogue()`), otherwise the prerequisites guard answers 302 / setup panel.
Deferred blocks: `getWithDeferred($url)` returns the page with its fragments.

## Assertions traps

- Values split by markup (`<bdi>`) → `assertSeeText`, not `assertSee`.
- Spaces in formatted numbers are U+202F / U+00A0 → build expectations with the helpers
  (`money()`, `$unit->format()`), never with a typed space.
- Never run a file-writing tool (`make:crud`) in tests: test it through a mocked service.
- Never leave orphan tables after a trial (`migrate:rollback --path=…` then delete files).

## Browser verification (Playwright, Python)

Run against a disposable database, never the user's:

```bash
DB_DATABASE=app_test APP_ENV=local php artisan migrate:fresh --seed --force
DB_DATABASE=app_test APP_ENV=local php artisan db:seed --class=DemoSeeder --force
DB_DATABASE=app_test php artisan serve --port=8765     # in background; stop with kill $(lsof -t -i:8765)
```

- The test suite empties the test database: reseed after running tests, before browsing.
- Repeated logins hit the rate limiter (429) — reuse one browser context.
- Sweep every GET route at 390 px: `document.documentElement.scrollWidth` must equal the
  viewport; no element with `overflow-x: auto|scroll` and `scrollWidth > clientWidth`
  (ignore tab strips); no visible `<table>` without `table-stack`.
- Lists at 640 / 768 / 900 / 1024 / 1280 / 1440 px; resize back and forth (cards ↔ table).
- Open the first row's panel on each list and repeat the overflow check inside
  `[data-side-panel]`.
- Arabic and French; measure bidi order (i18n-rtl.md); take screenshots and look at them.
- Modal confirm button: `[data-confirm-modal] [data-confirm-accept]`.
- **A panel that "opens" as an unstyled page at `/things/panel`** is not a routing problem:
  the panel loader falls back to navigating to the fragment when ANY error is thrown after
  the fetch (while enhancing widgets). Read the browser console (`page.on('console')`) —
  the loader logs the error before falling back. Typical cause: a widget that assumes a
  row/element exists (an empty repeater list). Open every panel that contains the changed
  widget, with and without existing rows.

## Finishing checklist

1. `vendor/bin/pint --format agent`
2. `npm run build`
3. `php artisan test --compact` — all green (a `public/hot` file from `npm run dev` can make a
   CSP-nonce test fail: not a regression, say so)
4. Journal entry (`docs/99-Journal.md`): date, what changed and why, files, tests, how it
   was verified
5. `docs/10-Endpoints.md` if routes changed; component docs if a component changed
6. Report honestly: what was verified in the browser, what was not, anything left to the
   user (migrations to run, `.env` to fill). Do not commit unless asked.
