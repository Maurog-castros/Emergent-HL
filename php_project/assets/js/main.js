/**
 * LogiSystem — Main JavaScript
 * Sidebar toggle, flash auto-dismiss, password visibility
 */
(function () {
    'use strict';

    // ── Sidebar Toggle ────────────────────────────────────────
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle  = document.getElementById('sidebarToggle');

    function openSidebar() {
        sidebar?.classList.add('open');
        overlay?.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('show');
        document.body.style.overflow = '';
    }

    toggle?.addEventListener('click', function () {
        sidebar?.classList.contains('open') ? closeSidebar() : openSidebar();
    });

    overlay?.addEventListener('click', closeSidebar);

    // Close sidebar on ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
    });

    // ── Flash Auto-dismiss (4 segundos) ──────────────────────
    const flash = document.getElementById('flashMessage');
    if (flash && typeof bootstrap !== 'undefined') {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(flash);
            bsAlert?.close();
        }, 4000);
    }

    // ── Password Visibility Toggle ────────────────────────────
    document.querySelectorAll('.toggle-pwd, #togglePassword').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const group = btn.closest('.input-group');
            const input = group?.querySelector('input[type="password"], input[type="text"]');
            const icon  = btn.querySelector('i');
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon?.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon?.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });

    // ── Login form: deshabilitar botón al enviar ───────────────
    const loginForm = document.getElementById('loginForm');
    const loginBtn  = document.getElementById('loginBtn');
    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function () {
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Ingresando...';
        });
    }

})();
