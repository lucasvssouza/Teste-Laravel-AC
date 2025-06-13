export function initThemeToggle() {
    const toggleBtn = document.querySelector('#theme-toggle');
    if (!toggleBtn) return;

    const themeText = localStorage.getItem('theme-text') || 'light';
    const themeBg = localStorage.getItem('theme-bg') || 'dark';

    applyTheme(themeText, themeBg);

    toggleBtn.addEventListener('click', () => {
        const currentText = localStorage.getItem('theme-text') || 'light';
        const currentBg = localStorage.getItem('theme-bg') || 'dark';

        const newText = currentText === 'light' ? 'dark' : 'light';
        const newBg = currentBg === 'dark' ? 'light' : 'dark';

        applyTheme(newText, newBg);
    });
}

export function applyTheme(text, bg) {
    document.querySelectorAll('[class*="theme-bg"], [class*="theme-text"]').forEach(el => {
        if (el.classList.contains('theme-bg')) {
            el.classList.remove('bg-light', 'bg-dark');
            el.classList.add(`bg-${bg}`);
        }

        if (el.classList.contains('theme-text')) {
            el.classList.remove('text-light', 'text-dark');
            el.classList.add(`text-${text}`);
        }
    });

    document.querySelectorAll('.theme-btn').forEach(btn => {
        btn.classList.remove('btn-outline-light', 'btn-outline-dark');
        btn.classList.add(`btn-outline-${text}`);
    });

    const toggleBtn = document.querySelector('#theme-toggle');
    if (toggleBtn) {
        toggleBtn.textContent = text === 'light' ? '🌙 Tema Escuro' : '☀️ Tema Claro';
    }

    localStorage.setItem('theme-text', text);
    localStorage.setItem('theme-bg', bg);
}
