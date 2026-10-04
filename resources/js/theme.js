import { storage } from './storage';

const html = document.documentElement;
const systemDark = window.matchMedia('(prefers-color-scheme: dark)');

/** The theme actually on screen: an explicit choice, or the system preference. */
export const effectiveTheme = () => html.dataset.theme || (systemDark.matches ? 'dark' : 'light');

function sync() {
    const choice = storage.get('theme') || 'system';
    const current = effectiveTheme();

    document.querySelectorAll('[data-set-theme]').forEach((button) => {
        button.setAttribute('aria-pressed', String(button.dataset.setTheme === choice));
    });
    document.querySelectorAll('[data-theme-icon]').forEach((icon) => {
        icon.classList.toggle('hidden', icon.dataset.themeIcon !== current);
    });
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const label = current === 'dark' ? 'Switch to light theme' : 'Switch to dark theme';
        button.setAttribute('aria-label', label);
        button.dataset.tip = label;
    });

    document.dispatchEvent(new CustomEvent('theme:change', { detail: { theme: current } }));
}

export function setTheme(mode) {
    if (mode === 'system') {
        delete html.dataset.theme;
        storage.set('theme', null);
    } else {
        html.dataset.theme = mode;
        storage.set('theme', mode);
    }
    sync();
}

export function initTheme() {
    document.addEventListener('click', (event) => {
        const picker = event.target.closest('[data-set-theme]');
        if (picker) {
            setTheme(picker.dataset.setTheme);
            return;
        }
        if (event.target.closest('[data-theme-toggle]')) {
            setTheme(effectiveTheme() === 'dark' ? 'light' : 'dark');
        }
    });

    systemDark.addEventListener('change', () => {
        if (!html.dataset.theme) sync();
    });

    sync();
}
