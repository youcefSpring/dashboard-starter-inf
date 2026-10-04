import { close as closeSidebar, computeRail } from './sidebar';
import { storage } from './storage';

const html = document.documentElement;

/** The active layout: 'horizontal' (top navigation, the default) or 'vertical' (sidebar). */
export const currentLayout = () => (html.dataset.layout === 'vertical' ? 'vertical' : 'horizontal');

function sync() {
    const layout = currentLayout();

    document.querySelectorAll('[data-set-layout]').forEach((button) => {
        button.setAttribute('aria-pressed', String(button.dataset.setLayout === layout));
    });
    document.querySelectorAll('[data-layout-toggle]').forEach((button) => {
        const label = layout === 'vertical' ? 'Switch to top navigation' : 'Switch to sidebar navigation';
        button.setAttribute('aria-label', label);
        button.dataset.tip = label;
    });
}

export function setLayout(layout) {
    if (layout === 'vertical') {
        html.dataset.layout = 'vertical';
        storage.set('layout', 'vertical');
    } else {
        delete html.dataset.layout;
        storage.set('layout', null);
    }
    closeSidebar();
    computeRail();
    sync();
    // Charts and other size-aware widgets listen for resize.
    window.dispatchEvent(new Event('resize'));
}

export function initLayout() {
    document.addEventListener('click', (event) => {
        const picker = event.target.closest('[data-set-layout]');
        if (picker) {
            setLayout(picker.dataset.setLayout);
            return;
        }
        if (event.target.closest('[data-layout-toggle]')) {
            setLayout(currentLayout() === 'vertical' ? 'horizontal' : 'vertical');
        }
    });

    sync();
}
