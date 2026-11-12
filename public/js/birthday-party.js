/******/ (() => { // webpackBootstrap
/*!****************************************!*\
  !*** ./resources/js/birthday-party.js ***!
  \****************************************/
(function (_banner$querySelector2) {
  var banner = document.querySelector('[data-birthday-banner]');
  if (!banner) return;
  var todayKey = banner.dataset.todayKey;
  try {
    if (localStorage.getItem(todayKey) === '1') banner.hidden = true;
  } catch (error) {
    // Keep the banner available when browser storage is disabled.
  }
  var garland = banner.querySelector('[data-birthday-garland]');
  var confetti = banner.querySelector('[data-birthday-confetti]');
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  var resizeFrame = 0;
  var hasPlayed = false;
  var isInViewport = false;
  var renderGarland = function renderGarland() {
    if (!garland) return;
    garland.replaceChildren();
    var width = banner.clientWidth;
    var count = Math.min(40, Math.ceil(width / 46));
    var heights = [24, 30, 36];
    for (var index = 0; index < count; index += 1) {
      var flag = document.createElement('span');
      flag.className = 'birthday-flag';
      flag.setAttribute('aria-hidden', 'true');
      flag.style.left = "".concat(Math.min(width - 22, index * 46 + 12), "px");
      flag.style.height = "".concat(heights[index % heights.length], "px");
      flag.style.animationDelay = "".concat(-(index % 8 * 0.17), "s");
      garland.appendChild(flag);
    }
  };
  var playConfetti = function playConfetti() {
    if (!confetti || banner.hidden || reduceMotion.matches || !banner.isConnected) return;
    confetti.replaceChildren();
    for (var index = 0; index < 30; index += 1) {
      var piece = document.createElement('span');
      piece.className = 'birthday-confetti-piece';
      piece.setAttribute('aria-hidden', 'true');
      piece.style.left = "".concat(Math.random() * 100, "%");
      piece.style.setProperty('--fall-time', "".concat(3.2 + Math.random() * 2.4, "s"));
      piece.style.setProperty('--fall-delay', "".concat(Math.random() * 1.6, "s"));
      piece.style.setProperty('--fall-rotation', "".concat(360 + Math.random() * 720, "deg"));
      confetti.appendChild(piece);
    }
    hasPlayed = true;
  };
  renderGarland();
  window.addEventListener('resize', function () {
    if (resizeFrame) cancelAnimationFrame(resizeFrame);
    resizeFrame = requestAnimationFrame(renderGarland);
  }, {
    passive: true
  });
  if (confetti) {
    var _banner$querySelector;
    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        isInViewport = entries.some(function (entry) {
          return entry.isIntersecting;
        });
        if (isInViewport) {
          if (!document.querySelector('#first-visit-tour:not([hidden])')) {
            playConfetti();
            observer.disconnect();
          }
        }
      }, {
        threshold: 0.2
      });
      observer.observe(banner);
      var tour = document.getElementById('first-visit-tour');
      if (tour) {
        var tourObserver = new MutationObserver(function () {
          if (isInViewport && !tour.hidden && !hasPlayed) return;
          if (isInViewport && tour.hidden && !hasPlayed) {
            playConfetti();
            observer.disconnect();
            tourObserver.disconnect();
          }
        });
        tourObserver.observe(tour, {
          attributes: true,
          attributeFilter: ['hidden']
        });
      }
    } else if (!banner.hidden) {
      playConfetti();
    }
    (_banner$querySelector = banner.querySelector('[data-birthday-replay]')) === null || _banner$querySelector === void 0 || _banner$querySelector.addEventListener('click', function () {
      hasPlayed = false;
      playConfetti();
    });
  }
  (_banner$querySelector2 = banner.querySelector('[data-birthday-close]')) === null || _banner$querySelector2 === void 0 || _banner$querySelector2.addEventListener('click', function () {
    try {
      localStorage.setItem(todayKey, '1');
    } catch (error) {
      // Hiding still works for this page when storage is disabled.
    }
    banner.hidden = true;
  });
})();
/******/ })()
;