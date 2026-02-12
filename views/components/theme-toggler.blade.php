<div class="theme-toggle" data-theme-toggle>
    <button type="button" class="theme-toggle-btn" data-theme-option="light" aria-label="Use light theme"
        title="Use light theme">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0M17 11a1 1 0 100-2h-1a1 1 0 100 2zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1M5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707m1.414 8.486-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414M4 11a1 1 0 100-2H3a1 1 0 000 2z"
                clip-rule="evenodd" />
        </svg>
    </button>

    <button type="button" class="theme-toggle-btn" data-theme-option="dark" aria-label="Use dark theme"
        title="Use dark theme">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
        </svg>
    </button>
</div>

<script>
    (() => {
        const storageKey = 'theme';
        const rootEl = document.documentElement;
        const toggleEl = document.querySelector('[data-theme-toggle]');

        if (!toggleEl) {
            return;
        }

        const buttons = Array.from(toggleEl.querySelectorAll('[data-theme-option]'));
        const storedTheme = (() => {
            try {
                return localStorage.getItem(storageKey);
            } catch (error) {
                return null;
            }
        })();

        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        let theme = storedTheme === 'light' || storedTheme === 'dark' ?
            storedTheme :
            (prefersDark ? 'dark' : 'light');

        const applyTheme = (nextTheme) => {
            theme = nextTheme;
            rootEl.classList.toggle('theme-dark', theme === 'dark');

            buttons.forEach((button) => {
                const isActive = button.getAttribute('data-theme-option') === theme;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });
        };

        applyTheme(theme);

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                const nextTheme = button.getAttribute('data-theme-option');
                if (nextTheme !== 'light' && nextTheme !== 'dark') {
                    return;
                }

                applyTheme(nextTheme);

                try {
                    localStorage.setItem(storageKey, nextTheme);
                } catch (error) {
                    // Ignore localStorage errors in restricted browsers.
                }
            });
        });
    })();
</script>
