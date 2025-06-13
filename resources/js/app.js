import 'bootstrap';
import jQuery  from'jquery';
import Swal from 'sweetalert2';

window.$ = jQuery;
window.Swal = Swal;

import { initThemeToggle, applyTheme } from './theme';
window.applyTheme = applyTheme;

document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();

    const themeText = localStorage.getItem('theme-text') || 'light';
    const themeBg = localStorage.getItem('theme-bg') || 'dark';
    applyTheme(themeText, themeBg);
});
