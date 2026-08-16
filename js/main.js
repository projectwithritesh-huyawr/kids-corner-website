// js/main.js — Login & register page interactions
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.field__toggle').forEach((toggle) => {
        const control = toggle.closest('.field__control');
        const input = control ? control.querySelector('input') : null;
        if (!input) return;

        const openEye = toggle.querySelector('.eye--open');
        const slashEye = toggle.querySelector('.eye--slash');

        toggle.addEventListener('click', () => {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            toggle.setAttribute('aria-pressed', String(show));
            toggle.title = show ? 'Hide password' : 'Show password';

            if (openEye) openEye.style.display = show ? 'none' : 'block';
            if (slashEye) slashEye.style.display = show ? 'block' : 'none';

            input.focus();
        });
    });
});
