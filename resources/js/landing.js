(function () {
    var page = document.querySelector('.lp-page');
    if (!page) return;

    var root = document.documentElement;
    var toggles = document.querySelectorAll('[data-theme-toggle]');
    var meta = document.querySelector('[data-theme-color]');
    var themeConfig = window.landingTheme || {};

    function paintTheme(theme) {
        root.dataset.theme = theme;
        toggles.forEach(function (button) {
            button.setAttribute('aria-checked', String(theme === 'light'));
            var label = button.querySelector('[data-theme-label]');
            if (label) label.textContent = theme === 'light' ? 'Nuit' : 'Jour';
        });
        if (meta) meta.content = theme === 'light' ? '#efe2c9' : '#111723';
    }

    var initialTheme = root.dataset.theme;
    if (initialTheme === 'auto') {
        initialTheme = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
    }
    paintTheme(initialTheme === 'light' ? 'light' : 'dark');

    toggles.forEach(function (button) {
        button.addEventListener('click', function () {
            var theme = root.dataset.theme === 'light' ? 'dark' : 'light';
            paintTheme(theme);
            document.cookie = 'theme=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
            if (themeConfig.authenticated && themeConfig.preferenceUrl) {
                window.fetch(themeConfig.preferenceUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': themeConfig.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ theme: theme }),
                    credentials: 'same-origin'
                }).catch(function () {});
            }
        });
    });

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduceMotion) {
        page.classList.add('lp-animate');
        var carousel = document.querySelector('[data-landing-carousel]');
        if (carousel) {
            var slides = Array.prototype.slice.call(carousel.querySelectorAll('.lp-slide'));
            var title = document.querySelector('[data-plaque-title]');
            var room = document.querySelector('[data-plaque-room]');
            var active = 0;
            if (slides.length > 1) {
                window.setInterval(function () {
                    slides[active].classList.remove('is-active');
                    active = (active + 1) % slides.length;
                    slides[active].classList.add('is-active');
                    if (title) title.textContent = slides[active].dataset.title;
                    if (room) room.textContent = slides[active].dataset.room;
                }, 4200);
            }
        }
    }
})();
