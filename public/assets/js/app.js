document.addEventListener('DOMContentLoaded', function () {
    var navToggle = document.querySelector('.nav-toggle');
    var nav = document.querySelector('.site-nav');

    if (navToggle && nav) {
        navToggle.addEventListener('click', function () {
            var isOpen = nav.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    // Placeholder: attach future AJAX handlers here
});

