const STORAGE_KEY = 'color-mode'

function getSystemPreference(): 'light' | 'dark' {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
}

function applyTheme(dark: boolean): void {
    document.documentElement.classList.toggle('dark', dark)
}

/**
 * Call once before Vue mounts to apply the correct theme immediately
 * and avoid a flash of the wrong theme.
 */
export function initColorMode(): void {
    const stored = localStorage.getItem(STORAGE_KEY)
    const isDark = stored === 'dark' || (stored !== 'light' && getSystemPreference() === 'dark')
    applyTheme(isDark)
}

export function useColorMode() {
    function isDark(): boolean {
        return document.documentElement.classList.contains('dark')
    }

    function toggle(): void {
        const next = !isDark()
        applyTheme(next)
        localStorage.setItem(STORAGE_KEY, next ? 'dark' : 'light')
    }

    // Keep in sync when system preference changes and no explicit preference is saved
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem(STORAGE_KEY)) {
            applyTheme(e.matches)
        }
    })

    return { isDark, toggle }
}
