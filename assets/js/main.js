/* ============================================================
   BAA ESTATES — Global scripts
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

    /* ----------------------------------------------------------
       Mobile navigation toggle
       ---------------------------------------------------------- */
    const toggle = document.querySelector('.nav-toggle');
    const nav    = document.getElementById('site-nav');

    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            const open = nav.classList.toggle('open');
            toggle.classList.toggle('open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        // Close nav when a link is clicked on mobile
        nav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 900) {
                    nav.classList.remove('open');
                    toggle.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        });
    }

    /* ----------------------------------------------------------
       Auto-submit filter selects
       ---------------------------------------------------------- */
    document.querySelectorAll('[data-auto-submit]').forEach(el => {
        el.addEventListener('change', () => el.closest('form')?.submit());
    });

    /* ----------------------------------------------------------
       Filters collapse on mobile
       ---------------------------------------------------------- */
    const filtersToggle = document.querySelector('.filters-toggle');
    const filters = document.getElementById('filters');
    if (filtersToggle && filters) {
        filtersToggle.addEventListener('click', () => {
            const open = filters.classList.toggle('open');
            filtersToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    /* ----------------------------------------------------------
       Auto-dismiss flash messages after 5 seconds
       ---------------------------------------------------------- */
    document.querySelectorAll('.flash').forEach(flash => {
        setTimeout(() => {
            flash.style.transition = 'opacity .4s, transform .4s';
            flash.style.opacity = '0';
            flash.style.transform = 'translateX(20px)';
            setTimeout(() => flash.remove(), 400);
        }, 5000);
    });

});