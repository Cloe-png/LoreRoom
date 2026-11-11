(function () {
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion) return;

    document.querySelectorAll('.auth-card').forEach(function (card) {
        var mascot = card.querySelector('.auth-mascot');
        if (!mascot) return;

        var timer;
        card.addEventListener('input', function (event) {
            if (!event.target.matches('input, textarea, select')) return;
            mascot.classList.add('talk');
            window.clearTimeout(timer);
            timer = window.setTimeout(function () {
                mascot.classList.remove('talk');
            }, 700);
        });
    });
})();
