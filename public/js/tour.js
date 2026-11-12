/******/ (() => { // webpackBootstrap
/*!******************************!*\
  !*** ./resources/js/tour.js ***!
  \******************************/
(function () {
  'use strict';

  // Animation setting: 'natural' uses speech-like timing; 'fixed' shows one frame per FRAME_MS.
  var MOUTH_MODE = 'natural';
  var FRAME_MS = 2000;
  var root = document.getElementById('first-visit-tour');
  if (!root) return;
  var dialog = root.querySelector('.tour-card');
  var frame = root.querySelector('.tour-frame');
  var title = root.querySelector('#tour-step-title');
  var reserve = root.querySelector('.tour-copy-reserve');
  var typed = root.querySelector('.tour-copy-typed');
  var announcer = root.querySelector('#tour-announcer');
  var counter = root.querySelector('[data-tour-counter]');
  var skipButton = root.querySelector('[data-tour-skip]');
  var previousButton = root.querySelector('[data-tour-previous]');
  var nextButton = root.querySelector('[data-tour-next]');
  var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var mascot = root.querySelector('.tour-mascot');
  var steps = [];
  var index = 0;
  var isOpen = false;
  var isTyping = false;
  var previousFocus = null;
  var typeTimer = null;
  var mouthTimer = null;
  var bounceTimer = null;
  var punctuationUntil = 0;
  var lastOpenFrame = 'talk2';
  var fixedFrameIndex = 0;
  var savedDetails = new Map();
  var currentDetails = [];
  function mascotFrame(name) {
    if (!mascot) return;
    mascot.querySelectorAll('[data-tour-frame]').forEach(function (image) {
      image.style.opacity = image.getAttribute('data-tour-frame') === name ? '1' : '0';
    });
  }
  function preloadFrames() {
    if (!mascot) return Promise.resolve();
    var images = Array.prototype.slice.call(mascot.querySelectorAll('img[data-tour-frame]'));
    return Promise.all(images.map(function (element) {
      var image = new Image();
      image.src = element.currentSrc || element.src;
      if (image.decode) return image.decode()["catch"](function () {});
      return new Promise(function (resolve) {
        if (image.complete) return resolve();
        image.onload = resolve;
        image.onerror = resolve;
      });
    }));
  }
  function stopMouth() {
    window.clearTimeout(mouthTimer);
    window.clearTimeout(bounceTimer);
    if (mascot) mascot.classList.remove('is-bouncing');
  }
  function bounceMouth() {
    if (!mascot || reducedMotion) return;
    mascot.classList.remove('is-bouncing');
    // Restart the 120 ms, 3 px bounce for each open frame.
    void mascot.offsetWidth;
    mascot.classList.add('is-bouncing');
    window.clearTimeout(bounceTimer);
    bounceTimer = window.setTimeout(function () {
      mascot.classList.remove('is-bouncing');
    }, 120);
  }
  function nextNaturalMouth() {
    var now = Date.now();
    if (now < punctuationUntil) {
      mascotFrame('closed');
      mouthTimer = window.setTimeout(nextNaturalMouth, Math.max(20, punctuationUntil - now));
      return;
    }
    if (Math.random() < 0.3) {
      mascotFrame('closed');
      mouthTimer = window.setTimeout(nextNaturalMouth, 110 + Math.random() * 90);
      return;
    }
    lastOpenFrame = lastOpenFrame === 'talk1' ? 'talk2' : 'talk1';
    mascotFrame(lastOpenFrame);
    bounceMouth();
    mouthTimer = window.setTimeout(nextNaturalMouth, 130 + Math.random() * 120);
  }
  function nextFixedMouth() {
    var sequence = ['talk1', 'talk2', 'closed', 'look'];
    mascotFrame(sequence[fixedFrameIndex % sequence.length]);
    fixedFrameIndex += 1;
    mouthTimer = window.setTimeout(nextFixedMouth, FRAME_MS);
  }
  function startMouth() {
    stopMouth();
    if (reducedMotion || !mascot) {
      mascotFrame('look');
      return;
    }
    if (MOUTH_MODE === 'fixed') {
      fixedFrameIndex = 0;
      nextFixedMouth();
    } else {
      nextNaturalMouth();
    }
  }
  function restoreCurrentDetails() {
    currentDetails.forEach(function (details) {
      if (savedDetails.has(details)) details.open = savedDetails.get(details);
    });
    currentDetails = [];
  }
  function prepareDetails(target) {
    restoreCurrentDetails();
    var details = target.closest('details');
    while (details) {
      if (!savedDetails.has(details)) savedDetails.set(details, details.open);
      details.open = true;
      currentDetails.push(details);
      details = details.parentElement ? details.parentElement.closest('details') : null;
    }
  }
  function restoreAllDetails() {
    restoreCurrentDetails();
    savedDetails.forEach(function (wasOpen, details) {
      details.open = wasOpen;
    });
    savedDetails.clear();
  }
  function updateFrame(target, circular) {
    if (!target) {
      frame.style.top = '0px';
      frame.style.left = '0px';
      frame.style.width = '0px';
      frame.style.height = '0px';
      frame.style.borderRadius = '';
      return;
    }
    var rect = target.getBoundingClientRect();
    if (circular) {
      var side = Math.max(rect.width, rect.height) + 10;
      frame.style.top = rect.top + (rect.height - side) / 2 + 'px';
      frame.style.left = rect.left + (rect.width - side) / 2 + 'px';
      frame.style.width = side + 'px';
      frame.style.height = side + 'px';
      frame.style.borderRadius = '50%';
      return;
    }
    frame.style.top = Math.max(0, rect.top - 2) + 'px';
    frame.style.left = Math.max(0, rect.left - 2) + 'px';
    frame.style.width = Math.max(0, rect.width) + 'px';
    frame.style.height = Math.max(0, rect.height) + 'px';
    frame.style.borderRadius = '';
  }
  function setNextLabel() {
    var step = steps[index] || {};
    nextButton.textContent = step.actionLabel || (index === steps.length - 1 ? window.tourLabels.finish : window.tourLabels.next);
    if (skipButton) skipButton.textContent = step.skipLabel || window.tourLabels.skip;
  }
  function finishTyping() {
    if (!isTyping) return;
    isTyping = false;
    window.clearTimeout(typeTimer);
    typed.textContent = steps[index].text;
    stopMouth();
    mascotFrame('look');
  }
  function typeText(text, position) {
    if (reducedMotion || !mascot) {
      typed.textContent = text;
      mascotFrame('look');
      isTyping = false;
      return;
    }
    isTyping = true;
    startMouth();
    var characters = Array.from(text);
    function writeNext() {
      if (!isOpen || index !== position) return;
      if (typed.textContent.length >= text.length) {
        finishTyping();
        return;
      }
      var offset = Array.from(typed.textContent).length;
      var character = characters[offset];
      typed.textContent += character;
      if (/[.,!?…]/.test(character)) {
        punctuationUntil = Date.now() + 220;
        mascotFrame('closed');
        typeTimer = window.setTimeout(writeNext, 220);
      } else {
        typeTimer = window.setTimeout(writeNext, 24);
      }
    }
    writeNext();
  }
  function showStep(nextIndex) {
    window.clearTimeout(typeTimer);
    stopMouth();
    isTyping = false;
    punctuationUntil = 0;
    restoreCurrentDetails();
    while (nextIndex >= 0 && nextIndex < steps.length) {
      var candidate = steps[nextIndex];
      var candidateTarget = candidate.target ? document.querySelector('[data-tour="' + candidate.target + '"]') : null;
      if (!candidate.target || candidateTarget) break;
      nextIndex += 1;
    }
    if (nextIndex < 0) nextIndex = 0;
    if (nextIndex >= steps.length) {
      completeTour();
      return;
    }
    index = nextIndex;
    var step = steps[index];
    var target = step.target ? document.querySelector('[data-tour="' + step.target + '"]') : null;
    if (target) {
      prepareDetails(target);
      target.scrollIntoView({
        block: 'nearest',
        inline: 'nearest'
      });
    }
    root.classList.toggle('is-centered', !target);
    title.textContent = step.title;
    reserve.textContent = step.text;
    typed.textContent = '';
    announcer.textContent = step.text;
    counter.textContent = window.tourLabels.counter.replace(':current', String(index + 1)).replace(':total', String(steps.length));
    previousButton.disabled = index === 0;
    setNextLabel();
    mascotFrame('look');
    window.requestAnimationFrame(function () {
      updateFrame(target, step.key === 'review');
      if (target && (target.getBoundingClientRect().width === 0 || target.getBoundingClientRect().height === 0)) {
        nextIndex += 1;
        showStep(nextIndex);
        return;
      }
      nextButton.focus();
      typeText(step.text, index);
    });
  }
  function postState(url, data) {
    return fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': root.getAttribute('data-csrf'),
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data || {})
    });
  }
  function closeTour() {
    isOpen = false;
    window.clearTimeout(typeTimer);
    stopMouth();
    restoreAllDetails();
    root.hidden = true;
    root.classList.remove('is-centered');
    document.body.classList.remove('first-visit-tour-active');
    if (previousFocus && typeof previousFocus.focus === 'function') previousFocus.focus();
  }
  function completeTour(data) {
    if (!isOpen) return Promise.resolve(null);
    root.setAttribute('data-completed', 'true');
    closeTour();
    return postState(root.getAttribute('data-complete-url'), data)["catch"](function () {
      return null;
    });
  }
  function openTour() {
    if (isOpen || !steps.length) return;
    isOpen = true;
    previousFocus = document.activeElement;
    savedDetails.clear();
    root.hidden = false;
    document.body.classList.add('first-visit-tour-active');
    showStep(0);
  }
  function advance() {
    if (isTyping) {
      finishTyping();
      return;
    }
    if (index >= steps.length - 1) {
      var action = steps[index].action || 'complete';
      if (action === 'create-world' || action === 'choose-world') {
        completeTour({
          create_world: action === 'create-world',
          choose_world: action === 'choose-world'
        }).then(function (response) {
          if (!response || !response.ok) return;
          response.json().then(function (result) {
            if (result.redirect) window.location.assign(result.redirect);
          });
        });
      } else {
        completeTour();
      }
      return;
    }
    showStep(index + 1);
  }
  function goPrevious() {
    if (index > 0) showStep(index - 1);
  }
  function trapFocus(event) {
    var controls = Array.prototype.slice.call(dialog.querySelectorAll('button:not(:disabled)'));
    if (!controls.length) return;
    var first = controls[0];
    var last = controls[controls.length - 1];
    if (event.shiftKey && (document.activeElement === first || !dialog.contains(document.activeElement))) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && (document.activeElement === last || !dialog.contains(document.activeElement))) {
      event.preventDefault();
      first.focus();
    }
  }
  root.querySelector('[data-tour-next]').addEventListener('click', advance);
  root.querySelector('[data-tour-previous]').addEventListener('click', goPrevious);
  root.querySelector('[data-tour-skip]').addEventListener('click', completeTour);
  root.querySelector('[data-tour-never]').addEventListener('click', completeTour);
  dialog.addEventListener('click', function (event) {
    if (event.target.closest('button')) return;
    if (isTyping) finishTyping();else advance();
  });
  document.addEventListener('keydown', function (event) {
    if (!isOpen) return;
    if (event.key === 'Escape') {
      event.preventDefault();
      completeTour();
    } else if (event.key === 'ArrowRight') {
      event.preventDefault();
      advance();
    } else if (event.key === 'ArrowLeft') {
      event.preventDefault();
      goPrevious();
    } else if (event.key === 'Tab') {
      trapFocus(event);
    }
  });
  window.addEventListener('resize', function () {
    if (!isOpen || root.classList.contains('is-centered')) return;
    var target = steps[index].target ? document.querySelector('[data-tour="' + steps[index].target + '"]') : null;
    updateFrame(target, steps[index].key === 'review');
  });
  window.addEventListener('scroll', function () {
    if (!isOpen || root.classList.contains('is-centered')) return;
    var target = steps[index].target ? document.querySelector('[data-tour="' + steps[index].target + '"]') : null;
    updateFrame(target, steps[index].key === 'review');
  }, true);
  document.querySelectorAll('[data-tour-restart]').forEach(function (button) {
    button.addEventListener('click', function () {
      button.disabled = true;
      if (isOpen) closeTour();
      postState(root.getAttribute('data-restart-url')).then(function (response) {
        if (!response.ok) throw new Error('Tutoriel indisponible');
        root.setAttribute('data-completed', 'false');
        openTour();
      })["catch"](function () {
        button.disabled = false;
      });
    });
  });
  var localeConfig = document.querySelector('[data-tour-steps]');
  try {
    steps = JSON.parse(localeConfig ? localeConfig.textContent : '[]');
  } catch (error) {
    steps = [];
  }
  window.tourLabels = {
    counter: root.getAttribute('data-label-counter') || ':current / :total',
    next: root.getAttribute('data-label-next') || 'Suivant',
    finish: root.getAttribute('data-label-finish') || 'Terminer',
    skip: root.getAttribute('data-label-skip') || 'Passer'
  };
  var preloaded = preloadFrames();
  if (root.getAttribute('data-auto-start') === 'true' && root.getAttribute('data-completed') !== 'true') {
    window.setTimeout(function () {
      preloaded.then(openTour);
    }, 500);
  }
})();
/******/ })()
;