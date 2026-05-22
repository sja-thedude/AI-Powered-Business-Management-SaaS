import { ref } from 'vue';

const STORAGE_KEY = 'novabiz-theme';
export const isDark = ref(false);

function apply(dark) {
    isDark.value = dark;
    document.documentElement.classList.toggle('dark', dark);
}

/** Resolve the initial theme from storage, then OS preference. */
export function initTheme() {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored) {
        apply(stored === 'dark');
        return;
    }
    apply(window.matchMedia('(prefers-color-scheme: dark)').matches);
}

export function toggleTheme() {
    const next = !isDark.value;
    apply(next);
    localStorage.setItem(STORAGE_KEY, next ? 'dark' : 'light');
}
