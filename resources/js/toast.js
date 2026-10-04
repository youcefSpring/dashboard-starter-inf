import { renderIcons } from './icons';

const ICONS = {
    success: ['circle-check', 'text-success'],
    error: ['circle-alert', 'text-error'],
    warning: ['triangle-alert', 'text-warning'],
    info: ['info', 'text-info'],
};

const escape = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);

function dismiss(toast) {
    toast.style.transition = 'opacity .15s, transform .15s';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(6px)';
    setTimeout(() => toast.remove(), 160);
}

/** Auto-dismisses a toast; hovering or focusing it pauses the timer. */
function arm(toast, duration) {
    let timer;
    const start = () => {
        clearTimeout(timer);
        timer = setTimeout(() => dismiss(toast), duration);
    };
    const pause = () => clearTimeout(timer);

    toast.addEventListener('pointerenter', pause);
    toast.addEventListener('pointerleave', start);
    toast.addEventListener('focusin', pause);
    toast.addEventListener('focusout', start);
    toast.querySelector('[data-toast-close]')?.addEventListener('click', () => dismiss(toast));
    start();
}

/** Show a toast from JavaScript: toast({ type: 'success'|'error'|'warning'|'info', title, text }). */
export function toast({ type = 'success', title, text = '', duration = 5000 }) {
    const root = document.getElementById('toast-root');
    if (!root) return;
    const [icon, color] = ICONS[type] || ICONS.info;

    const el = document.createElement('div');
    el.dataset.toast = '';
    el.setAttribute('role', type === 'error' ? 'alert' : 'status');
    el.className = 'toast-in pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border border-line bg-surface p-3.5 shadow-pop sm:w-[380px]';
    el.innerHTML = `<span class="mt-px ${color}"><i data-lucide="${icon}" class="size-5"></i></span>
        <div class="min-w-0 flex-1"><p class="text-sm font-semibold">${escape(title)}</p>${text ? `<p class="mt-0.5 text-[13px] text-fg-2">${escape(text)}</p>` : ''}</div>
        <button type="button" data-toast-close aria-label="Dismiss" class="-mr-1 -mt-1 grid size-7 shrink-0 place-items-center rounded-md text-fg-3 transition-colors hover:bg-surface-2 hover:text-fg"><i data-lucide="x" class="size-4"></i></button>`;

    root.appendChild(el);
    while (root.children.length > 3) root.firstElementChild.remove();
    renderIcons(el);
    arm(el, duration);
}

export function initToasts(duration = 5000) {
    document.querySelectorAll('#toast-root [data-toast]').forEach((el) => arm(el, duration));
    window.toast = toast;

    // Declarative trigger: <button data-toast-demo='{"type":"info","title":"Hello"}'>.
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-toast-demo]');
        if (trigger) toast(JSON.parse(trigger.dataset.toastDemo));
    });
}
