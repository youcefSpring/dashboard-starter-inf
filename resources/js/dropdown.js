/**
 * Dropdowns: [data-dropdown] wraps a [data-dropdown-trigger] and a [data-dropdown-panel].
 * Closes on outside click, Escape, or focus leaving; arrow keys move between menu items.
 */
let openDropdown = null;

const items = (panel) => [...panel.querySelectorAll('[role="menuitem"], [data-set-theme], a[href], button:not([disabled])')];

/** Floating panels escape overflow clipping: fixed to the viewport, aligned to the trigger, flipped if needed. */
function place(dropdown) {
    if (!dropdown.hasAttribute('data-dropdown-floating')) return;
    const panel = dropdown.querySelector('[data-dropdown-panel]');
    const rect = dropdown.querySelector('[data-dropdown-trigger]').getBoundingClientRect();
    const { offsetWidth: w, offsetHeight: h } = panel;
    const gap = 6;

    let top = rect.bottom + gap;
    if (top + h > window.innerHeight - 8 && rect.top - gap - h > 8) top = rect.top - gap - h;
    let left = dropdown.dataset.align === 'start' ? rect.left : rect.right - w;
    left = Math.min(Math.max(left, 8), window.innerWidth - w - 8);

    Object.assign(panel.style, { position: 'fixed', top: `${top}px`, left: `${left}px`, right: 'auto', bottom: 'auto', margin: '0' });
}

function close(dropdown, restoreFocus = false) {
    if (!dropdown) return;
    const trigger = dropdown.querySelector('[data-dropdown-trigger]');
    dropdown.querySelector('[data-dropdown-panel]').hidden = true;
    trigger.setAttribute('aria-expanded', 'false');
    if (restoreFocus) trigger.focus();
    if (openDropdown === dropdown) openDropdown = null;
}

function open(dropdown) {
    if (openDropdown && openDropdown !== dropdown) close(openDropdown);
    const panel = dropdown.querySelector('[data-dropdown-panel]');
    panel.hidden = false;
    place(dropdown);
    dropdown.querySelector('[data-dropdown-trigger]').setAttribute('aria-expanded', 'true');
    openDropdown = dropdown;
}

export function initDropdowns() {
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-dropdown-trigger]');
        if (trigger) {
            const dropdown = trigger.closest('[data-dropdown]');
            dropdown.querySelector('[data-dropdown-panel]').hidden ? open(dropdown) : close(dropdown);
            return;
        }
        if (openDropdown && !openDropdown.contains(event.target)) close(openDropdown);
    });

    document.addEventListener('keydown', (event) => {
        if (!openDropdown) return;
        const panel = openDropdown.querySelector('[data-dropdown-panel]');

        if (event.key === 'Escape') {
            event.preventDefault();
            close(openDropdown, true);
        } else if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            const list = items(panel);
            if (!list.length) return;
            const index = list.indexOf(document.activeElement);
            const next = event.key === 'ArrowDown' ? (index + 1) % list.length : (index - 1 + list.length) % list.length;
            list[index === -1 && event.key === 'ArrowUp' ? list.length - 1 : next].focus();
        }
    });

    // Keep floating panels attached to their trigger while the page scrolls; close once it scrolls away.
    const follow = (event) => {
        if (!openDropdown?.hasAttribute('data-dropdown-floating')) return;
        if (event?.target instanceof Node && openDropdown.querySelector('[data-dropdown-panel]').contains(event.target)) return;
        const rect = openDropdown.querySelector('[data-dropdown-trigger]').getBoundingClientRect();
        if (rect.bottom < 0 || rect.top > window.innerHeight) close(openDropdown);
        else place(openDropdown);
    };
    window.addEventListener('scroll', follow, { passive: true, capture: true });
    window.addEventListener('resize', follow);

    document.addEventListener('focusin', (event) => {
        if (openDropdown && !openDropdown.contains(event.target)) close(openDropdown);
    });
}
