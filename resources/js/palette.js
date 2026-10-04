/** Command palette: Ctrl/⌘ K or [data-palette-open] opens #command-palette; type to filter, arrows + Enter to run. */
export function initPalette() {
    const dialog = document.getElementById('command-palette');
    if (!dialog) return;

    const input = dialog.querySelector('[data-palette-input]');
    const items = [...dialog.querySelectorAll('[data-palette-item]')];
    const empty = dialog.querySelector('[data-palette-empty]');
    const search = dialog.querySelector('[data-palette-search]');
    const searchLabel = dialog.querySelector('[data-palette-search-label]');
    const isMac = /Mac|iPhone|iPad/.test(navigator.platform || navigator.userAgent);
    let index = 0;

    document.querySelectorAll('[data-shortcut-label]').forEach((kbd) => {
        if (isMac) kbd.textContent = '⌘ K';
    });

    const visibleItems = () => items.filter((item) => !item.hidden && item.offsetParent !== null);

    function highlight(next) {
        const list = visibleItems();
        index = Math.max(0, Math.min(next, list.length - 1));
        items.forEach((item) => item.setAttribute('aria-selected', 'false'));
        const current = list[index];
        if (current) {
            current.setAttribute('aria-selected', 'true');
            current.scrollIntoView({ block: 'nearest' });
        }
    }

    function filter() {
        const query = input.value.trim().toLowerCase();
        items.forEach((item) => {
            // The "search users" action always matches and carries the query along.
            item.hidden = item !== search && query !== '' && !item.dataset.keywords.toLowerCase().includes(query);
        });
        if (search) {
            search.href = query ? `${search.dataset.paletteSearch}?q=${encodeURIComponent(input.value.trim())}` : search.dataset.paletteSearch;
            searchLabel.textContent = query ? `Search users for “${input.value.trim()}”` : 'Search users';
        }
        dialog.querySelectorAll('[data-palette-group]').forEach((group) => {
            group.hidden = ![...group.querySelectorAll('[data-palette-item]')].some((item) => !item.hidden);
        });
        empty.hidden = visibleItems().length > 0;
        highlight(0);
    }

    function open() {
        if (dialog.open) return;
        input.value = '';
        dialog.showModal();
        filter();
        input.focus();
    }

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-palette-open]')) open();
        else if (event.target.closest('[data-palette-item]') && dialog.contains(event.target)) {
            // Buttons (theme/layout toggles) run through their own handlers; just close the palette.
            setTimeout(() => dialog.close());
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key.toLowerCase() === 'k' && (isMac ? event.metaKey : event.ctrlKey)) {
            event.preventDefault();
            dialog.open ? dialog.close() : open();
        }
    });

    input.addEventListener('input', filter);
    input.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            highlight(index + (event.key === 'ArrowDown' ? 1 : -1));
        } else if (event.key === 'Enter') {
            event.preventDefault();
            visibleItems()[index]?.click();
        }
    });

    dialog.addEventListener('pointermove', (event) => {
        const item = event.target.closest('[data-palette-item]');
        if (item) highlight(visibleItems().indexOf(item));
    });
}
