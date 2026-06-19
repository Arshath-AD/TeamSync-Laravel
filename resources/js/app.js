import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

/* ─── NOTE: toggleTheme() is defined in the inline <script> in each layout  ─
   This ensures it works synchronously before this module loads.              ─
   Do NOT redefine toggleTheme here — it would overwrite the inline version.  ─
   ──────────────────────────────────────────────────────────────────────────*/

/* ─── Sync theme icons after DOM is ready ───────────────────────────────── */
function syncThemeIcons() {
    const isDark = document.documentElement.classList.contains('dark');
    document.querySelectorAll('.icon-sun').forEach(el  => el.classList.toggle('hidden', !isDark));
    document.querySelectorAll('.icon-moon').forEach(el => el.classList.toggle('hidden', isDark));
}

/* ─── Task View Toggle (Board / List) ───────────────────────────────────── */
window.initTaskViewToggle = function () {
    const saved  = localStorage.getItem('ts-task-view') || 'board';
    const boards = document.querySelectorAll('[data-view="board"]');
    const lists  = document.querySelectorAll('[data-view="list"]');
    const btnBrd = document.getElementById('view-btn-board');
    const btnLst = document.getElementById('view-btn-list');

    function show(view) {
        localStorage.setItem('ts-task-view', view);
        boards.forEach(el => el.classList.toggle('hidden', view !== 'board'));
        lists.forEach(el  => el.classList.toggle('hidden', view !== 'list'));
        if (btnBrd) btnBrd.classList.toggle('active', view === 'board');
        if (btnLst) btnLst.classList.toggle('active', view === 'list');
    }

    if (btnBrd) btnBrd.addEventListener('click', () => show('board'));
    if (btnLst) btnLst.addEventListener('click', () => show('list'));

    show(saved);
};

/* ─── Password Visibility Toggle ────────────────────────────────────────── */
window.initPasswordToggle = function () {
    document.querySelectorAll('[data-pw-toggle]').forEach(btn => {
        const targetId = btn.getAttribute('data-pw-toggle');
        const input    = document.getElementById(targetId);
        if (!input) return;
        btn.addEventListener('click', () => {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.querySelector('.eye-open').classList.toggle('hidden', !isHidden);
            btn.querySelector('.eye-closed').classList.toggle('hidden', isHidden);
        });
    });
};

/* ─── Status Quick-Update ───────────────────────────────────────────────── */
window.initStatusUpdate = function () {
    document.querySelectorAll('[data-status-form]').forEach(form => {
        const select = form.querySelector('select');
        if (!select) return;
        select.addEventListener('change', () => form.submit());
    });
};

/* ─── Flash dismissal ───────────────────────────────────────────────────── */
window.initFlash = function () {
    document.querySelectorAll('[data-flash]').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });
};

/* ─── Profile dropdown ──────────────────────────────────────────────────── */
window.initProfileDropdown = function () {
    const trigger = document.getElementById('profile-trigger');
    const menu    = document.getElementById('profile-menu');
    if (!trigger || !menu) return;

    trigger.addEventListener('click', e => {
        e.stopPropagation();
        menu.classList.toggle('hidden');
    });

    document.addEventListener('click', () => menu.classList.add('hidden'));
};

/* ─── Init all on DOM ready ──────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    syncThemeIcons();          // sync icon state with current theme
    initPasswordToggle();
    initStatusUpdate();
    initFlash();
    initProfileDropdown();
    if (document.getElementById('view-btn-board')) {
        initTaskViewToggle();
    }
});

Alpine.start();
