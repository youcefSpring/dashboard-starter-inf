/** Lightweight tooltips for any element with [data-tip]. Rail-only tips use [data-tip-when="rail"]. */
let timer = null;
let current = null;

function tooltipEl() {
    return document.getElementById('tooltip');
}

function show(target) {
    const tip = tooltipEl();
    if (!tip || !target.isConnected) return;
    if (target.dataset.tipWhen === 'rail' && !document.documentElement.classList.contains('rail')) return;

    tip.textContent = target.dataset.tip;
    tip.classList.remove('hidden');

    const rect = target.getBoundingClientRect();
    const { offsetWidth: w, offsetHeight: h } = tip;
    let x;
    let y;

    if (target.dataset.tipSide === 'right') {
        x = rect.right + 10;
        y = rect.top + rect.height / 2 - h / 2;
    } else {
        x = rect.left + rect.width / 2 - w / 2;
        y = rect.top - h - 8;
        if (y < 8) y = rect.bottom + 8;
    }

    tip.style.left = `${Math.min(Math.max(x, 8), window.innerWidth - w - 8)}px`;
    tip.style.top = `${Math.min(Math.max(y, 8), window.innerHeight - h - 8)}px`;
}

function hide() {
    clearTimeout(timer);
    current = null;
    tooltipEl()?.classList.add('hidden');
}

export function initTooltips() {
    if (!tooltipEl()) return;

    document.addEventListener('pointerover', (event) => {
        if (event.pointerType === 'touch') return;
        const target = event.target.closest('[data-tip]');
        if (target === current) return;
        hide();
        if (!target || target.getAttribute('aria-expanded') === 'true') return;
        current = target;
        timer = setTimeout(() => show(target), 380);
    });

    document.addEventListener('focusin', (event) => {
        hide();
        const target = event.target.closest?.('[data-tip]');
        if (target && target.matches(':focus-visible')) {
            current = target;
            show(target);
        }
    });

    document.addEventListener('pointerdown', hide, true);
    window.addEventListener('scroll', hide, { passive: true, capture: true });
}
