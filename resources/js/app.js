document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.querySelector('[data-nav-toggle]');
    const navMenu = document.querySelector('[data-nav-menu]');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            const isOpen = !navMenu.classList.contains('hidden');

            navMenu.classList.toggle('hidden', isOpen);
            navMenu.classList.toggle('flex', !isOpen);
            navToggle.setAttribute('aria-expanded', String(!isOpen));
        });
    }

    document.querySelectorAll('[data-dropdown]').forEach((dropdown) => {
        const toggle = dropdown.querySelector('[data-dropdown-toggle]');
        const menu = dropdown.querySelector('[data-dropdown-menu]');

        if (!toggle || !menu) {
            return;
        }

        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const isOpen = !menu.classList.contains('hidden');

            document.querySelectorAll('[data-dropdown-menu]').forEach((otherMenu) => {
                if (otherMenu !== menu) {
                    otherMenu.classList.add('hidden');
                }
            });

            menu.classList.toggle('hidden', isOpen);
            toggle.setAttribute('aria-expanded', String(!isOpen));
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('[data-dropdown-menu]').forEach((menu) => {
            menu.classList.add('hidden');
        });
    });
});
