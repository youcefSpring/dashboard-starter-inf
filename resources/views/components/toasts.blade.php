{{--
    Toast stack. Flash `status` (a success message) or `toast` (['type' => ..., 'title' => ..., 'text' => ...])
    to the session and it appears here; resources/js/toast.js handles dismissal and window.toast().
--}}
<div id="toast-root" class="pointer-events-none fixed inset-x-4 bottom-6 z-[95] flex flex-col items-center gap-2 sm:inset-x-auto sm:right-6 sm:items-end" aria-live="polite">
    @if (session('status'))
        <x-toast :title="session('status')" />
    @endif
    @if (is_array(session('toast')))
        <x-toast :type="session('toast')['type'] ?? 'success'" :title="session('toast')['title']" :text="session('toast')['text'] ?? null" />
    @endif
</div>
