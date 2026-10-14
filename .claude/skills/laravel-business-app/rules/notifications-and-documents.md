# E-mails, notifications and PDF documents

## Mail configuration

- Dev: `MAIL_MAILER=log` (mails in `storage/logs/laravel.log`); tests: `array` (phpunit.xml).
- **Gmail** (works from a home/office connection):
  ```
  MAIL_MAILER=smtp
  MAIL_SCHEME=null
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=587
  MAIL_USERNAME=address@gmail.com
  MAIL_PASSWORD="xxxx xxxx xxxx xxxx"     # an APP PASSWORD (2-step verification on), not the account password
  MAIL_FROM_ADDRESS=address@gmail.com
  ```
- **Shared hosting (cPanel) blocks outgoing SMTP** to Gmail and others ("Network is
  unreachable" on 465, "Connection refused" on 587). Use the domain's own mailbox:
  ```
  MAIL_SCHEME=smtps
  MAIL_HOST=mail.example.com
  MAIL_PORT=465
  MAIL_USERNAME=noreply@example.com
  MAIL_PASSWORD='p@$$word'               # single quotes when it contains $
  MAIL_FROM_ADDRESS=noreply@example.com  # must be the authenticated mailbox
  ```
  Fallback: `MAIL_HOST=localhost`, `MAIL_PORT=25`, `MAIL_SCHEME=null`. Diagnose with
  `php -r '$f=@fsockopen("ssl://smtp.gmail.com",465,$e,$s,10); echo $f?"OK":$s;'`.
  Mails in spam → SPF/DKIM ("Email Deliverability" in cPanel).
- After editing `.env`: `php artisan config:clear`. Test:
  `php artisan tinker --execute 'Mail::raw("test", fn ($m) => $m->to("x@y.z")->subject("Test")); echo "OK";'`
- **Never put real credentials in `.env.example`** (tracked by git).
- Password reset: own notification (`ResetPasswordNotification`) with translated texts;
  `PasswordResetLinkController` catches `TransportExceptionInterface` and shows a clear
  message instead of a 500.

## Activity notifications (pattern)

`App\Services\Notifications\ActivityNotifier` decides who receives what;
`App\Notifications\ActivityNotification` only formats (subject, lines, optional button,
optional PDF attachment).

```php
// Controller, AFTER the service:
$sale = $this->sales->create($request->validated(), $request->user()->id, $confirm);
if ($confirm) {
    $this->notifier->saleConfirmed($sale, $request->user());
}
```

Rules:
- **After the response**: `defer(fn () => …)` — the user never waits for SMTP.
- **Never breaks a save**: the deferred closure wraps everything in `try { … } catch
  (Throwable $e) { report($e); }`.
- **One recipient never blocks another**: each send has its OWN try/report (`attempt()`).
  With a single try around everything, the SMTP server refusing one fictitious admin
  address (550) silently cancelled the supplier's e-mail sent after it. Test it with real
  sends on the `array` mailer and a `NotificationSending` listener that throws for one
  recipient.
- **Default locale**: `Localizable::withLocale(config('app.locale'), $send)`.
- **Called from controllers**, not services: seeders and commands send nothing.
- Drafts send nothing; the event is the *confirmation* (create-confirmed, confirm button,
  draft corrected and confirmed — three call sites, don't miss one).
- Recipients: users with the admin role and an e-mail (`Notification::send($admins, …)`);
  external parties (customer, supplier) only if their record has a valid address, through
  `Notification::route('mail', [$email => $name])` — no button (they have no account).
- The PDF is generated **once** per event and attached to every recipient.
- Admin accounts need real addresses in production (seeded `admin@…` addresses bounce).

## PDF documents

- One service per document family (`App\Services\Documents\DocumentPdfService`): default
  texts (`saleTexts()`), eager loading (`loadSale()`), file name (`saleFilename()`), and the
  file itself (`saleFile()` → `['name' => …, 'content' => …]`). The download action AND the
  e-mail use it → the attachment is exactly the downloaded document.
- `App\Support\Pdf`: `download()`, `inline()` (preview/print), `content()` (bytes for
  attachments), all through one `render()`.
- Confirmed document → its PDF downloads by itself (`saved()` payload `download`, consumed
  by `auto-download.js`, works from a panel or after a redirect).
- Admins may retouch PDF texts (title, party, notes, terms, signatures) in a panel before
  downloading (`CustomizesPdf`); defaults come from the document service.
- List printing: the browser print dialog on the same list fragment is the most reliable
  path for Arabic; the dompdf export applies the Arabic shaping (i18n-rtl.md).

## Tests

```php
Notification::fake();
// admins
Notification::assertSentTo($admin, ActivityNotification::class, fn ($n) => str_starts_with($n->attachment['content'], '%PDF'));
// external party
Notification::assertSentOnDemand(ActivityNotification::class, fn ($n, $channels, AnonymousNotifiable $to) => $to->routes['mail'] === ['client@example.com' => 'Name'] && $n->action === null);
// failure never breaks the save
$this->mock(Pdf::class)->shouldReceive('content')->andThrow(new RuntimeException('smtp down'));
```

Deferred callbacks run at the end of each test request (the HTTP kernel terminates).
