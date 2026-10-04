import { toast } from './toast';

/**
 * Data tables ([data-table-root], see x-table.root):
 *  - selection: [data-table-select-all] + [data-table-select] row checkboxes, a bulk bar
 *    ([data-table-bulk]) that replaces the summary, and [data-table-bulk-form] forms that
 *    receive the selected values as ids[] when submitted (they may live outside the root,
 *    e.g. in a modal; link them with data-table="{root id}").
 *  - client mode ([data-client]): search ([data-table-search]), multi-select filters
 *    ([data-table-filter]), sortable headers ([data-sort-key]) and row removal, all in the browser.
 */
const rowsOf = (root) => [...root.querySelectorAll('[data-table-body] > tr:not([data-table-empty])')];
const visibleRows = (root) => rowsOf(root).filter((row) => !row.hidden);
const checkboxOf = (row) => row.querySelector('[data-table-select]');

function selectedValues(root) {
    return rowsOf(root).map(checkboxOf).filter((box) => box?.checked).map((box) => box.value);
}

function syncSelection(root) {
    const boxes = visibleRows(root).map(checkboxOf).filter((box) => box && !box.disabled);
    const checked = boxes.filter((box) => box.checked).length;
    const total = selectedValues(root).length;

    rowsOf(root).forEach((row) => row.toggleAttribute('data-selected', !!checkboxOf(row)?.checked));

    const all = root.querySelector('[data-table-select-all]');
    if (all) {
        all.checked = boxes.length > 0 && checked === boxes.length;
        all.indeterminate = checked > 0 && checked < boxes.length;
    }

    const bulk = root.querySelector('[data-table-bulk]');
    const summary = root.querySelector('[data-table-summary]');
    if (bulk) {
        bulk.hidden = total === 0;
        if (summary) summary.hidden = total > 0;
    }
    root.querySelectorAll('[data-table-selected-count]').forEach((el) => {
        el.textContent = total;
    });
    document.querySelectorAll(`[data-table="${root.id}"] [data-table-selected-count]`).forEach((el) => {
        el.textContent = total;
    });
}

function clearSelection(root) {
    rowsOf(root).forEach((row) => {
        const box = checkboxOf(row);
        if (box) box.checked = false;
    });
    syncSelection(root);
}

/* ───────── Client mode ───────── */
function applyClientState(root) {
    const state = root._tableState;
    const query = state.query.trim().toLowerCase();
    const rows = rowsOf(root);

    rows.forEach((row) => {
        const matchesQuery = !query || (row.dataset.search || row.textContent).toLowerCase().includes(query);
        const matchesFilters = Object.entries(state.filters).every(([key, values]) => !values.size || values.has(row.dataset[`filter${key[0].toUpperCase()}${key.slice(1)}`]));
        row.hidden = !(matchesQuery && matchesFilters);
        // Hidden rows drop out of the selection.
        if (row.hidden && checkboxOf(row)) checkboxOf(row).checked = false;
    });

    if (state.sort.key) {
        const attr = `sort${state.sort.key[0].toUpperCase()}${state.sort.key.slice(1)}`;
        const value = (row) => {
            const raw = row.dataset[attr] ?? '';
            const number = Number(raw);
            return raw !== '' && !Number.isNaN(number) ? number : raw.toLowerCase();
        };
        const body = root.querySelector('[data-table-body]');
        rows.sort((a, b) => {
            const x = value(a);
            const y = value(b);
            return (x > y ? 1 : x < y ? -1 : 0) * (state.sort.dir === 'asc' ? 1 : -1);
        }).forEach((row) => body.insertBefore(row, body.querySelector('[data-table-empty]')));
    }

    root.querySelectorAll('[data-sort-key]').forEach((button) => {
        const dir = button.dataset.sortKey === state.sort.key ? state.sort.dir : null;
        button.closest('th')?.setAttribute('aria-sort', dir ? (dir === 'asc' ? 'ascending' : 'descending') : 'none');
        button.classList.toggle('text-fg', !!dir);
        button.querySelectorAll('[data-sort-icon]').forEach((icon) => {
            icon.classList.toggle('hidden', icon.dataset.sortIcon !== (dir || 'none'));
        });
    });

    root.querySelectorAll('[data-table-filter]').forEach((option) => {
        option.setAttribute('aria-checked', String(state.filters[option.dataset.tableFilter]?.has(option.dataset.value) ?? false));
    });
    root.querySelectorAll('[data-table-filter-chip]').forEach((chip) => {
        const count = state.filters[chip.dataset.tableFilterChip]?.size ?? 0;
        chip.classList.toggle('border-primary/40', count > 0);
        chip.classList.toggle('bg-primary-soft', count > 0);
        chip.classList.toggle('text-primary-ink', count > 0);
    });

    const visible = visibleRows(root).length;
    root.querySelectorAll('[data-table-count]').forEach((el) => {
        el.textContent = visible;
    });
    root.querySelector('[data-table-empty]')?.toggleAttribute('hidden', visible > 0);
    root.querySelectorAll('[data-table-reset]').forEach((el) => {
        el.hidden = !(query || Object.values(state.filters).some((values) => values.size));
    });

    syncSelection(root);
}

function initClient(root) {
    root._tableState = { query: '', filters: {}, sort: { key: null, dir: 'asc' } };
    const state = root._tableState;

    root.querySelector('[data-table-search]')?.addEventListener('input', (event) => {
        state.query = event.target.value;
        applyClientState(root);
    });

    root.addEventListener('click', (event) => {
        const filter = event.target.closest('[data-table-filter]');
        if (filter) {
            const values = (state.filters[filter.dataset.tableFilter] ??= new Set());
            values.has(filter.dataset.value) ? values.delete(filter.dataset.value) : values.add(filter.dataset.value);
            applyClientState(root);
            return;
        }

        const clearFilter = event.target.closest('[data-table-filter-clear]');
        if (clearFilter) {
            state.filters[clearFilter.dataset.tableFilterClear]?.clear();
            applyClientState(root);
            return;
        }

        const sort = event.target.closest('[data-sort-key]');
        if (sort) {
            const key = sort.dataset.sortKey;
            state.sort = { key, dir: state.sort.key === key && state.sort.dir === 'asc' ? 'desc' : 'asc' };
            applyClientState(root);
            return;
        }

        if (event.target.closest('[data-table-reset]')) {
            state.query = '';
            state.filters = {};
            const search = root.querySelector('[data-table-search]');
            if (search) search.value = '';
            applyClientState(root);
        }
    });

    applyClientState(root);
}

/** Remove rows from a client table (demo deletes), then refresh counts and selection. */
function removeRows(root, rows, label) {
    if (!rows.length) return;
    rows.forEach((row) => row.remove());
    if (root._tableState) applyClientState(root);
    else syncSelection(root);
    toast({ type: 'success', title: label, text: 'This demo table only changes in your browser.' });
}

export function initTables() {
    const roots = [...document.querySelectorAll('[data-table-root]')];

    roots.forEach((root) => {
        if (root.hasAttribute('data-client')) initClient(root);
        syncSelection(root);
    });

    document.addEventListener('change', (event) => {
        const root = event.target.closest('[data-table-root]');
        if (!root) return;

        if (event.target.matches('[data-table-select-all]')) {
            visibleRows(root).map(checkboxOf).forEach((box) => {
                if (box && !box.disabled) box.checked = event.target.checked;
            });
        }
        if (event.target.matches('[data-table-select-all], [data-table-select]')) syncSelection(root);
    });

    document.addEventListener('click', (event) => {
        const clear = event.target.closest('[data-table-clear]');
        if (clear) {
            clearSelection(clear.closest('[data-table-root]'));
            return;
        }

        const removeRow = event.target.closest('[data-table-remove-row]');
        if (removeRow) {
            const row = document.getElementById(removeRow.dataset.tableRemoveRow);
            const root = row?.closest('[data-table-root]');
            if (root) removeRows(root, [row], removeRow.dataset.toast || 'Row removed');
            return;
        }

        const removeSelected = event.target.closest('[data-table-remove-selected]');
        if (removeSelected) {
            const root = document.getElementById(removeSelected.dataset.tableRemoveSelected) || removeSelected.closest('[data-table-root]');
            const rows = rowsOf(root).filter((row) => checkboxOf(row)?.checked);
            removeRows(root, rows, `${rows.length} ${rows.length === 1 ? 'row' : 'rows'} removed`);
        }
    });

    // Bulk forms receive the selection as ids[].
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('[data-table-bulk-form]');
        if (!form) return;
        const root = document.getElementById(form.dataset.table) || form.closest('[data-table-root]');
        if (!root) return;
        form.querySelectorAll('input[data-bulk-id]').forEach((input) => input.remove());
        selectedValues(root).forEach((value) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = value;
            input.dataset.bulkId = '';
            form.appendChild(input);
        });
    }, true);
}
