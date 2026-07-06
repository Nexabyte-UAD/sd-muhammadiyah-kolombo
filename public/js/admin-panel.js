(() => {
    const body = document.body;
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const closeTargets = document.querySelectorAll('[data-sidebar-close]');

    const setSidebar = (open) => {
        body.classList.toggle('sidebar-open', open);
        toggle?.setAttribute('aria-expanded', open ? 'true' : 'false');
    };

    toggle?.addEventListener('click', () => setSidebar(!body.classList.contains('sidebar-open')));
    closeTargets.forEach((target) => target.addEventListener('click', () => setSidebar(false)));

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) setSidebar(false);
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
