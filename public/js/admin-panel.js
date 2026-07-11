/**
 * Script Interaktivitas Panel Admin (public/js/admin-panel.js)
 * 
 * Mengatur interaktivitas peniadaan/togle sidebar (sidebar collapsing) baik untuk desktop maupun mobile,
 * penyimpanan preferensi togle di LocalStorage (persistence state), sistem togle Mode Gelap (dark mode),
 * serta penanganan disabilitas tombol submit ganda saat form dikirimkan untuk mencegah duplikasi request.
 */
(() => {
    const body = document.body;
    const toggles = document.querySelectorAll('[data-sidebar-toggle]');
    const closeTargets = document.querySelectorAll('[data-sidebar-close]');

    const setSidebar = (open) => {
        if (window.innerWidth >= 1024) {
            body.classList.toggle('sidebar-collapsed', !open);
            localStorage.setItem('sidebar-collapsed', !open ? 'true' : 'false');
        } else {
            body.classList.toggle('sidebar-open', open);
            toggles.forEach(t => t.setAttribute('aria-expanded', open ? 'true' : 'false'));
        }
    };

    // Load persisted state on desktop
    if (window.innerWidth >= 1024) {
        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isCollapsed) {
            body.classList.add('sidebar-collapsed');
        }
    }

    toggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            if (window.innerWidth >= 1024) {
                setSidebar(body.classList.contains('sidebar-collapsed'));
            } else {
                setSidebar(!body.classList.contains('sidebar-open'));
            }
        });
    });

    // Expand sidebar when clicking on it while collapsed on desktop
    const sidebarEl = document.getElementById('adminSidebar');
    sidebarEl?.addEventListener('click', (e) => {
        if (window.innerWidth >= 1024 && body.classList.contains('sidebar-collapsed')) {
            if (!e.target.closest('[data-sidebar-toggle]')) {
                setSidebar(true);
            }
        }
    });

    closeTargets.forEach((target) => target.addEventListener('click', () => setSidebar(false)));

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            body.classList.remove('sidebar-open');
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            body.classList.toggle('sidebar-collapsed', isCollapsed);
        } else {
            body.classList.remove('sidebar-collapsed');
        }
    });

    // Theme toggle system (Dark/Light Mode)
    const themeToggleBtn = document.getElementById('theme-toggle');
    themeToggleBtn?.addEventListener('click', () => {
        const isDark = document.documentElement.classList.contains('dark-mode');
        if (isDark) {
            document.documentElement.classList.remove('dark-mode');
            localStorage.setItem('admin-theme', 'light');
        } else {
            document.documentElement.classList.add('dark-mode');
            localStorage.setItem('admin-theme', 'dark');
        }
    });

    document.addEventListener('submit', (event) => {
        queueMicrotask(() => {
            if (event.defaultPrevented) return;

            event.target.querySelectorAll('button[type="submit"]').forEach((button) => {
                button.disabled = true;
                button.setAttribute('aria-busy', 'true');
                button.classList.add('is-submitting');
            });
        });
    });
})();
