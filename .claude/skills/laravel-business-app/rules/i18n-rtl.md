# Languages, RTL, numbers

## Translations

- Locales `ar` (default, RTL), `fr`, `en`; choice stored in session (`SetLocale`
  middleware, `PUT /locale`). E-mails use the default locale (`config('app.locale')`).
- **Every user-visible string goes through `__()`**, with the key in the **three** files
  `lang/{ar,fr,en}/<module>.php` — including validation attributes, toasts, PDF texts,
  e-mail texts, tooltips, `aria-label`s.
- `lang/{ar,fr}.json` for Laravel's own strings (mail template footer…).
- `TranslationCoverageTest` checks keys exist everywhere and rejects an `ar` value copied
  from `fr`.
- Arabic business vocabulary chosen with the user (units علبة / شوالة / لفة…); keep demo and
  test data in that vocabulary.

## RTL layout

- `<html dir="rtl">` when the locale is in `config('app.rtl_locales')`.
- Only logical utilities: `ps-/pe-/ms-/me-/start-/end-/border-s/border-e`,
  `text-start/text-end`, `rtl:rotate-180` on arrows. Never `left/right/pl/pr/ml/mr`.
- Animations (drawers, shimmer) read CSS variables inverted in RTL (`--slide-from`).
- Row tone bar: `inset 4px 0 0` in LTR, `inset -4px 0 0` in RTL.

## Numbers, units, signs — the `.num` class

```css
.num { direction: ltr; unicode-bidi: plaintext; word-spacing: 0.2em; font-feature-settings: 'tnum' 1; }
```

- `plaintext`: the **first letter decides the direction** of each value.
  - A bare number, amount or date ("-90", "15 379.96", "12/09/2026") has no letter → stays
    left-to-right (otherwise Arabic shows "90-").
  - A number followed by an Arabic unit ("65 قطعة", "1 علبة = 10 قطعة") reads right-to-left,
    number first. Forced LTR, it read backwards: "قطعة 65", "علبة = 10 قطعة 1".
- `direction: ltr` stays for layout; alignment is fixed explicitly
  (`html[dir=rtl] .num.text-end { text-align: left }` and the LTR counterpart) because with
  `plaintext` start/end follow each value.
- `word-spacing`: the font's space is under 2 px; the unit looked glued ("65قطعة").
- Separators: thousands = U+202F (thin no-break space, `money()`); value ↔ unit = U+00A0
  (`Unit::format`, `formatQuantity`). Tests compare via the helpers, never a typed space.
- **Signed quantities with a unit** ("+0.5 م", "-2 علبة"): `{{ quantity_html($text) }}` wraps the
  number and its sign in `<bdi dir="ltr">`; otherwise the sign jumps to the other side next
  to Arabic. HTML only — PDFs/CSV get plain text (dompdf may draw isolate characters as boxes).
- Latin fragment inside an Arabic sentence where markup is impossible (attribute, toast):
  `bidi($text)` (FSI … PDI).
- In tests, when markup splits a value (`<bdi>`), assert with `assertSeeText`.

## Verify, don't guess

Bidi bugs are invisible in the source. Measure in a real browser (Playwright): for each
word of a cell, a `Range` → `getBoundingClientRect().left`, sort right-to-left, compare
with the expected reading order. Check Arabic AND French (alignment), desktop AND phone.

```js
// in page.evaluate: tokens of a cell, in visual order right → left
const w = document.createTreeWalker(cell, NodeFilter.SHOW_TEXT); const parts = [];
let n; while ((n = w.nextNode())) { const re = /[^\s  ]+/g; let m;
  while ((m = re.exec(n.textContent))) { const r = document.createRange();
    r.setStart(n, m.index); r.setEnd(n, m.index + m[0].length);
    parts.push([m[0], r.getBoundingClientRect().left]); } }
return parts.sort((a, b) => b[1] - a[1]).map(p => p[0]).join(' ← ');
```

## Arabic in PDFs

dompdf knows neither Arabic letter joining nor RTL. `App\Support\Pdf::prepare()` rewrites
the HTML before rendering: every text containing Arabic goes through
`ArPHP\I18N\Arabic::utf8Glyphs()` (shaped, visual order) and, in Arabic, table cells are
reversed so the first column lands on the right. Screen-only elements (svg, nav, inputs,
buttons, `[data-print-hidden]`) are removed. Font `DejaVu Sans`, remote assets disabled.
