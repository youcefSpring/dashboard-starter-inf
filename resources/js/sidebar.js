import { storage } from './storage';

const html = document.documentElement;

const isVertical = () => html.dataset.layout === 'vertical';

/**
 * Sidebar behaviour by width.
 * Vertical layout:   < 768px drawer · 768–1023px icon rail that expands as an overlay · ≥ 1024px full, collapsible (remembered)
 * Horizontal layout: < 1024px drawer · ≥ 1024px hidden (navigation lives in the top bar)
 */
export function computeRail() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    const width = window.innerWidth;
    if (width >= 1024) html.classList.remove('sb-open');

    const open = html.classList.contains('sb-open');
    const vertical = isVertical();
    const rail = vertical && ((width >= 768 && width < 1024 && !open) || (width >= 1024 && html.classList.contains('sb-collapsed')));
    const drawerOnly = vertical ? width < 768 : width < 1024;

    html.classList.toggle('rail', rail);
    document.getElementById('sb-overlay')?.classList.toggle('hidden', !(open && width < 1024));
    sidebar.inert = (drawerOnly && !open) || (!vertical && width >= 1024);

    const toggle = document.getElementById('sb-toggle');
    if (toggle) {
        toggle.setAttribute('aria-label', rail ? 'Expand sidebar' : 'Collapse sidebar');
        toggle.setAttribute('aria-expanded', String(!rail));
        toggle.querySelector('[data-expanded-icon]')?.classList.toggle('hidden', rail);
        toggle.querySelector('[data-collapsed-icon]')?.classList.toggle('hidden', !rail);
    }
}

function open() {
    html.classList.add('sb-open');
    computeRail();
    document.querySelector('#sidebar a, #sidebar button')?.focus();
}

export function close() {
    html.classList.remove('sb-open');
    computeRail();
}

export function initSidebar() {
    if (!document.getElementById('sidebar')) return;

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-sidebar-open]')) open();
        else if (event.target.closest('[data-sidebar-close]')) close();
        else if (event.target.closest('[data-sidebar-toggle]')) {
            if (window.innerWidth >= 1024) {
                const collapsed = html.classList.toggle('sb-collapsed');
                storage.set('sidebar-collapsed', collapsed ? '1' : null);
                computeRail();
            } else {
                html.classList.contains('sb-open') ? close() : open();
            }
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && html.classList.contains('sb-open')) close();
    });

    window.addEventListener('resize', computeRail);
    computeRail();
}
