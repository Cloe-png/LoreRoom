(() => {
    const banner = document.querySelector('[data-birthday-banner]');
    if (!banner) return;

    const todayKey = banner.dataset.todayKey;
    try {
        if (localStorage.getItem(todayKey) === '1') banner.hidden = true;
    } catch (error) {
        // Keep the banner available when browser storage is disabled.
    }

    const garland = banner.querySelector('[data-birthday-garland]');
    const confetti = banner.querySelector('[data-birthday-confetti]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let resizeFrame = 0;
    let hasPlayed = false;
    let isInViewport = false;

    const renderGarland = () => {
        if (!garland) return;
        garland.replaceChildren();
        const width = banner.clientWidth;
        const count = Math.min(40, Math.ceil(width / 46));
        const heights = [24, 30, 36];
        for (let index = 0; index < count; index += 1) {
            const flag = document.createElement('span');
            flag.className = 'birthday-flag';
            flag.setAttribute('aria-hidden', 'true');
            flag.style.left = `${Math.min(width - 22, index * 46 + 12)}px`;
            flag.style.height = `${heights[index % heights.length]}px`;
            flag.style.animationDelay = `${-((index % 8) * 0.17)}s`;
            garland.appendChild(flag);
        }
    };

    const playConfetti = () => {
        if (!confetti || banner.hidden || reduceMotion.matches || !banner.isConnected) return;
        confetti.replaceChildren();
        for (let index = 0; index < 30; index += 1) {
            const piece = document.createElement('span');
            piece.className = 'birthday-confetti-piece';
            piece.setAttribute('aria-hidden', 'true');
            piece.style.left = `${Math.random() * 100}%`;
            piece.style.setProperty('--fall-time', `${3.2 + Math.random() * 2.4}s`);
            piece.style.setProperty('--fall-delay', `${Math.random() * 1.6}s`);
            piece.style.setProperty('--fall-rotation', `${360 + Math.random() * 720}deg`);
            confetti.appendChild(piece);
        }
        hasPlayed = true;
    };

    renderGarland();
    window.addEventListener('resize', () => {
        if (resizeFrame) cancelAnimationFrame(resizeFrame);
        resizeFrame = requestAnimationFrame(renderGarland);
    }, { passive: true });

    if (confetti) {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(entries => {
                isInViewport = entries.some(entry => entry.isIntersecting);
                if (isInViewport) {
                    if (!document.querySelector('#first-visit-tour:not([hidden])')) {
                        playConfetti();
                        observer.disconnect();
                    }
                }
            }, { threshold: 0.2 });
            observer.observe(banner);
            const tour = document.getElementById('first-visit-tour');
            if (tour) {
                const tourObserver = new MutationObserver(() => {
                    if (isInViewport && !tour.hidden && !hasPlayed) return;
                    if (isInViewport && tour.hidden && !hasPlayed) {
                        playConfetti();
                        observer.disconnect();
                        tourObserver.disconnect();
                    }
                });
                tourObserver.observe(tour, { attributes: true, attributeFilter: ['hidden'] });
            }
        } else if (!banner.hidden) {
            playConfetti();
        }

        banner.querySelector('[data-birthday-replay]')?.addEventListener('click', () => {
            hasPlayed = false;
            playConfetti();
        });
    }

    banner.querySelector('[data-birthday-close]')?.addEventListener('click', () => {
        try {
            localStorage.setItem(todayKey, '1');
        } catch (error) {
            // Hiding still works for this page when storage is disabled.
        }
        banner.hidden = true;
    });
})();
