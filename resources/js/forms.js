const spinner =
    '<svg class="size-4 spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity=".25" stroke-width="3"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';

/**
 * Prefill a dialog from its opener's data-dialog-fill JSON, so one modal serves every table row:
 *  - "action"  sets the action of the dialog's forms
 *  - any key   sets matching form fields (by name) and [data-fill="key"] text
 */
function fillDialog(dialog, data) {
    Object.entries(data).forEach(([key, value]) => {
        if (key === 'action') {
            dialog.querySelectorAll('form').forEach((form) => {
                form.action = value;
            });
            return;
        }
        dialog.querySelectorAll(`[name="${key}"]`).forEach((field) => {
            field.value = value ?? '';
        });
        dialog.querySelectorAll(`[data-fill="${key}"]`).forEach((el) => {
            el.textContent = value ?? '';
        });
    });
    // Stale validation messages belong to another row.
    dialog.querySelectorAll('[aria-invalid="true"]').forEach((field) => field.removeAttribute('aria-invalid'));
    dialog.querySelectorAll('[id$="-error"]').forEach((error) => error.remove());
}

/**
 * Forms with [data-auto-submit] submit on change, and on typing after a pause (search boxes).
 * Focus returns to the same field after the reload so typing can continue.
 */
function initAutoSubmit() {
    let timer;
    const submit = (form, field) => {
        try {
            sessionStorage.setItem('refocus', field.name);
        } catch {
            /* storage unavailable */
        }
        form.requestSubmit();
    };

    document.addEventListener('input', (event) => {
        const form = event.target.closest('form[data-auto-submit]');
        if (!form || event.target.type !== 'search') return;
        clearTimeout(timer);
        timer = setTimeout(() => submit(form, event.target), 450);
    });
    document.addEventListener('change', (event) => {
        const form = event.target.closest('form[data-auto-submit]');
        if (form && event.target.tagName === 'SELECT') submit(form, event.target);
    });

    try {
        const name = sessionStorage.getItem('refocus');
        sessionStorage.removeItem('refocus');
        const field = name && document.querySelector(`form[data-auto-submit] [name="${name}"]`);
        if (field) {
            field.focus();
            if (field.type === 'search') field.setSelectionRange?.(field.value.length, field.value.length);
        }
    } catch {
        /* storage unavailable */
    }
}

/** Password reveal buttons, submit loading states, auto-submitting filters and native <dialog> (x-modal / x-drawer) wiring. */
export function initForms() {
    initAutoSubmit();

    // [data-copy="text"] copies to the clipboard and confirms with a toast.
    document.addEventListener('click', async (event) => {
        const copy = event.target.closest('[data-copy]');
        if (!copy) return;
        try {
            await navigator.clipboard.writeText(copy.dataset.copy);
            window.toast?.({ type: 'success', title: 'Copied to clipboard', text: copy.dataset.copy });
        } catch {
            window.toast?.({ type: 'error', title: 'Could not copy', text: 'Your browser blocked clipboard access.' });
        }
    });

    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-password-toggle]');
        if (toggle) {
            const input = document.getElementById(toggle.dataset.passwordToggle);
            const reveal = input.type === 'password';
            input.type = reveal ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', String(reveal));
            toggle.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
            toggle.querySelector('[data-show]')?.classList.toggle('hidden', reveal);
            toggle.querySelector('[data-hide]')?.classList.toggle('hidden', !reveal);
            return;
        }

        const opener = event.target.closest('[data-dialog-open]');
        if (opener) {
            const dialog = document.getElementById(opener.dataset.dialogOpen);
            if (!dialog) return;
            if (opener.dataset.dialogFill) fillDialog(dialog, JSON.parse(opener.dataset.dialogFill));
            dialog.showModal();
            return;
        }

        const closer = event.target.closest('[data-dialog-close]');
        if (closer) closer.closest('dialog')?.close();
    });

    // Clicking the dialog backdrop closes it.
    document.querySelectorAll('dialog').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) dialog.close();
        });
        if (dialog.hasAttribute('data-open-on-load')) dialog.showModal();
    });

    // Disable submit buttons and show a spinner while the request is in flight.
    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!form.matches('[data-loading-form]') || event.defaultPrevented) return;
        const button = event.submitter || form.querySelector('[type="submit"]');
        if (!button || !button.dataset.loadingLabel) return;
        button.dataset.originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `${spinner}<span>${button.dataset.loadingLabel}</span>`;
    });

    // Restore busy buttons when the page comes back from the back/forward cache.
    window.addEventListener('pageshow', (event) => {
        if (!event.persisted) return;
        document.querySelectorAll('[data-original-html]').forEach((button) => {
            button.innerHTML = button.dataset.originalHtml;
            button.disabled = false;
            delete button.dataset.originalHtml;
        });
    });
}
