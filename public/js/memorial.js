(function () {
    const button = document.querySelector('.memorial-secret');
    if (!button) return;
    let presses = [];
    button.addEventListener('click', function () {
        const now = Date.now();
        presses = presses.filter((time) => now - time < 1200);
        presses.push(now);
        if (presses.length >= 3) {
            window.location.assign(button.dataset.destination);
            return;
        }
        button.classList.remove('wiggle');
        void button.offsetWidth;
        button.classList.add('wiggle');
    });
})();
