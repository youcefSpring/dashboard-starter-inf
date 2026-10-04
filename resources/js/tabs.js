/**
 * Client-side tabs: inside a [data-tabs] list, buttons with [data-tab-target] show the matching
 * x-tab-panel and hide its siblings. Arrow keys, Home and End move between tabs.
 */
function activate(tab, focus = false) {
    const list = tab.closest('[data-tabs]');
    list.querySelectorAll('[data-tab-target]').forEach((other) => {
        const on = other === tab;
        other.setAttribute('aria-selected', String(on));
        other.tabIndex = on ? 0 : -1;
        const panel = document.getElementById(other.dataset.tabTarget);
        if (panel) panel.hidden = !on;
    });
    if (focus) tab.focus();
}

export function initTabs() {
    document.addEventListener('click', (event) => {
        const tab = event.target.closest('[data-tabs] [data-tab-target]');
        if (tab) activate(tab);
    });

    document.addEventListener('keydown', (event) => {
        const tab = event.target.closest?.('[data-tabs] [data-tab-target]');
        if (!tab || !['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
        event.preventDefault();
        const tabs = [...tab.closest('[data-tabs]').querySelectorAll('[data-tab-target]')];
        const index = tabs.indexOf(tab);
        const next = {
            ArrowLeft: (index - 1 + tabs.length) % tabs.length,
            ArrowRight: (index + 1) % tabs.length,
            Home: 0,
            End: tabs.length - 1,
        }[event.key];
        activate(tabs[next], true);
    });
}
