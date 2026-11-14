/******/ (() => { // webpackBootstrap
/*!********************************!*\
  !*** ./resources/js/museum.js ***!
  \********************************/
document.addEventListener('DOMContentLoaded', function () {
  var room = document.querySelector('.mus-room');
  if (!room) return;
  var paintings = JSON.parse(room.querySelector('[data-paintings]').textContent || '[]');
  var stage = room.querySelector('[data-stage]');
  var previousButton = room.querySelector('[data-previous]');
  var nextButton = room.querySelector('[data-next]');
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var current = Number(room.dataset.activeIndex || 0);
  var tourTimer = null;
  var pointerStart = null;
  function useDefaultOpening() {
    room.style.setProperty('--mus-it', '8.5%');
    room.style.setProperty('--mus-il', '11%');
    room.style.setProperty('--mus-ir', '11%');
    room.style.setProperty('--mus-ib', '8.5%');
  }
  function setFrame() {
    var frame = new Image();
    frame.onload = function () {
      var ratio = frame.naturalWidth / frame.naturalHeight;
      room.style.setProperty('--mus-frame-ratio', "".concat(frame.naturalWidth, " / ").concat(frame.naturalHeight));
      try {
        var width = Math.min(500, frame.naturalWidth);
        var height = Math.max(1, Math.round(width / ratio));
        var canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        var context = canvas.getContext('2d', {
          willReadFrequently: true
        });
        if (!context) throw new Error('Canvas unavailable');
        context.drawImage(frame, 0, 0, width, height);
        var pixels = context.getImageData(0, 0, width, height).data;
        var alphaAt = function alphaAt(x, y) {
          return pixels[(y * width + x) * 4 + 3];
        };
        var centerX = Math.floor(width / 2);
        var centerY = Math.floor(height / 2);
        if (alphaAt(centerX, centerY) >= 128) throw new Error('Opaque frame center');
        var left = centerX;
        var right = centerX;
        var top = centerY;
        var bottom = centerY;
        while (left > 0 && alphaAt(left, centerY) < 128) left -= 1;
        while (right < width - 1 && alphaAt(right, centerY) < 128) right += 1;
        while (top > 0 && alphaAt(centerX, top) < 128) top -= 1;
        while (bottom < height - 1 && alphaAt(centerX, bottom) < 128) bottom += 1;
        var margin = function margin(value) {
          return "".concat(Math.max(0, value - 0.5).toFixed(2), "%");
        };
        room.style.setProperty('--mus-il', margin(left / width * 100));
        room.style.setProperty('--mus-ir', margin((width - right) / width * 100));
        room.style.setProperty('--mus-it', margin(top / height * 100));
        room.style.setProperty('--mus-ib', margin((height - bottom) / height * 100));
      } catch (error) {
        useDefaultOpening();
      }
    };
    frame.onerror = function () {
      return useDefaultOpening();
    };
    frame.src = room.dataset.frameUrl;
  }
  function createSlot(item, index) {
    var slot = document.createElement('article');
    slot.className = 'mus-slot';
    slot.dataset.index = String(index);
    slot.setAttribute('role', 'group');
    slot.setAttribute('aria-roledescription', 'diapositive');
    slot.setAttribute('aria-label', "".concat(item.name, ", ").concat(index + 1, " sur ").concat(paintings.length));
    var frame = document.createElement('figure');
    frame.className = 'mus-fr';
    var art = document.createElement('button');
    art.className = 'mus-art';
    art.type = 'button';
    art.setAttribute('aria-label', "Afficher ".concat(item.name));
    var silhouette = document.createElement('span');
    silhouette.className = 'mus-silhouette';
    silhouette.textContent = item.initials || '?';
    silhouette.hidden = Boolean(item.image_url);
    silhouette.setAttribute('aria-hidden', String(Boolean(item.image_url)));
    art.appendChild(silhouette);
    if (item.image_url) {
      var portrait = document.createElement('img');
      portrait.className = 'mus-portrait';
      portrait.src = item.image_url;
      portrait.alt = "Portrait de ".concat(item.name);
      portrait.loading = index === current ? 'eager' : 'lazy';
      portrait.addEventListener('error', function () {
        portrait.hidden = true;
        silhouette.hidden = false;
        silhouette.setAttribute('aria-hidden', 'false');
      }, {
        once: true
      });
      art.appendChild(portrait);
    }
    var frameImage = document.createElement('img');
    frameImage.className = 'mus-cd';
    frameImage.src = room.dataset.frameUrl;
    frameImage.alt = '';
    frameImage.setAttribute('aria-hidden', 'true');
    frameImage.draggable = false;
    frameImage.addEventListener('error', function () {
      return frame.classList.add('mus-fr--fallback');
    }, {
      once: true
    });
    frame.append(art, frameImage);
    var plaque = document.createElement('section');
    plaque.className = 'mus-plaque';
    var name = document.createElement('h2');
    name.className = 'mus-name';
    name.textContent = item.name;
    var dates = [item.birth_year, item.death_year].filter(Boolean).join(' — ');
    var details = document.createElement('p');
    details.className = 'mus-details';
    details.textContent = "Salle des ".concat(item.room || 'Inclassables').concat(dates ? " \xB7 ".concat(dates) : '');
    var description = document.createElement('p');
    description.className = 'mus-description';
    description.textContent = item.description || 'Aucune description pour l’instant.';
    plaque.append(name, details, description);
    if (item.can_edit) {
      var editButton = document.createElement('button');
      editButton.className = 'mus-edit-button';
      editButton.type = 'button';
      editButton.textContent = 'Écrire / Modifier la description';
      plaque.appendChild(editButton);
      var form = document.createElement('form');
      form.className = 'mus-edit-form';
      form.method = 'POST';
      form.action = item.update_url;
      form.hidden = true;
      var token = document.createElement('input');
      token.type = 'hidden';
      token.name = '_token';
      token.value = room.dataset.csrfToken;
      var method = document.createElement('input');
      method.type = 'hidden';
      method.name = '_method';
      method.value = 'PUT';
      var descriptionOnly = document.createElement('input');
      descriptionOnly.type = 'hidden';
      descriptionOnly.name = 'gallery_description_only';
      descriptionOnly.value = '1';
      var label = document.createElement('label');
      label.textContent = 'Description du portrait';
      var input = document.createElement('textarea');
      input.name = 'portrait_description';
      input.maxLength = 280;
      input.rows = 3;
      input.value = item.description || '';
      label.appendChild(input);
      var actions = document.createElement('div');
      actions.className = 'mus-edit-actions';
      var save = document.createElement('button');
      save.className = 'mus-save';
      save.type = 'submit';
      save.textContent = 'Enregistrer';
      var cancel = document.createElement('button');
      cancel.className = 'mus-cancel';
      cancel.type = 'button';
      cancel.textContent = 'Annuler';
      actions.append(save, cancel);
      form.append(token, method, descriptionOnly, label, actions);
      plaque.appendChild(form);
    }
    var link = document.createElement('a');
    link.className = 'mus-sheet-link';
    link.href = item.sheet_url;
    link.textContent = 'Ouvrir la fiche';
    plaque.appendChild(link);
    slot.append(frame, plaque);
    return slot;
  }
  function stopTour() {
    if (tourTimer) window.clearInterval(tourTimer);
    tourTimer = null;
    var button = room.querySelector('[data-tour]');
    if (button) {
      button.textContent = 'Visite guidée';
      button.setAttribute('aria-pressed', 'false');
    }
  }
  function updateControls(item) {
    previousButton.disabled = current === 0;
    nextButton.disabled = current >= paintings.length - 1;
    room.querySelector('[data-counter]').textContent = "".concat(current + 1, " / ").concat(paintings.length);
    room.querySelectorAll('[data-jump]').forEach(function (button) {
      if (Number(button.dataset.jump) === current) button.setAttribute('aria-current', 'true');else button.removeAttribute('aria-current');
    });
    room.querySelector('[data-live]').textContent = "".concat(item.name, ", ").concat(current + 1, " sur ").concat(paintings.length, ". ").concat(item.description || '');
    var url = new URL(window.location.href);
    url.searchParams.set('p', item.id);
    window.history.replaceState({}, '', url);
    room.dataset.activeIndex = String(current);
  }
  function render() {
    if (!paintings.length) return;
    current = Math.max(0, Math.min(paintings.length - 1, current));
    var indexes = [current - 1, current, current + 1].filter(function (index) {
      return index >= 0 && index < paintings.length;
    });
    var fragment = document.createDocumentFragment();
    indexes.forEach(function (index) {
      var slot = createSlot(paintings[index], index);
      var distance = index - current;
      slot.classList.toggle('mus-slot--active', distance === 0);
      slot.classList.toggle('mus-slot--previous', distance < 0);
      slot.classList.toggle('mus-slot--next', distance > 0);
      slot.style.setProperty('--mus-offset', "".concat(distance * (window.innerWidth < 701 ? 185 : 285), "px"));
      slot.style.setProperty('--mus-scale', distance === 0 ? '1' : '0.67');
      slot.style.setProperty('--mus-layer', distance === 0 ? '3' : '1');
      slot.addEventListener('click', function (event) {
        if (!event.target.closest('.mus-art')) return;
        if (index !== current) {
          stopTour();
          go(index);
        }
      });
      fragment.appendChild(slot);
    });
    stage.replaceChildren(fragment);
    var item = paintings[current];
    updateControls(item);
  }
  function go(index) {
    if (index < 0 || index >= paintings.length || index === current) return;
    current = index;
    render();
  }
  previousButton.addEventListener('click', function () {
    stopTour();
    go(current - 1);
  });
  nextButton.addEventListener('click', function () {
    stopTour();
    go(current + 1);
  });
  room.querySelectorAll('[data-jump]').forEach(function (button) {
    button.addEventListener('click', function () {
      stopTour();
      go(Number(button.dataset.jump));
    });
  });
  room.addEventListener('keydown', function (event) {
    if (event.target.matches('input, textarea, select, [contenteditable="true"]')) return;
    if (event.key === 'ArrowLeft') {
      event.preventDefault();
      stopTour();
      go(current - 1);
    }
    if (event.key === 'ArrowRight') {
      event.preventDefault();
      stopTour();
      go(current + 1);
    }
    if (event.key === 'Home') {
      event.preventDefault();
      stopTour();
      go(0);
    }
    if (event.key === 'End') {
      event.preventDefault();
      stopTour();
      go(paintings.length - 1);
    }
  });
  if (!reducedMotion) {
    room.addEventListener('pointerdown', function (event) {
      if (event.target.closest('button, a, input, textarea')) return;
      pointerStart = event.clientX;
    });
    room.addEventListener('pointerup', function (event) {
      if (pointerStart === null) return;
      var delta = event.clientX - pointerStart;
      pointerStart = null;
      if (Math.abs(delta) >= 50) {
        stopTour();
        go(current + (delta < 0 ? 1 : -1));
      }
    });
    room.addEventListener('pointercancel', function () {
      pointerStart = null;
    });
  }
  var tourButton = room.querySelector('[data-tour]');
  if (paintings.length === 1) {
    room.querySelector('[data-miniframes]').hidden = true;
    tourButton.hidden = true;
  } else if (reducedMotion) {
    tourButton.hidden = true;
  } else {
    tourButton.addEventListener('click', function () {
      if (tourTimer) {
        stopTour();
        return;
      }
      tourButton.textContent = 'Arrêter la visite';
      tourButton.setAttribute('aria-pressed', 'true');
      tourTimer = window.setInterval(function () {
        return go((current + 1) % paintings.length);
      }, 5500);
    });
  }
  room.addEventListener('click', function (event) {
    var edit = event.target.closest('.mus-edit-button');
    if (edit) {
      stopTour();
      var form = edit.parentElement.querySelector('.mus-edit-form');
      form.hidden = false;
      edit.hidden = true;
      form.querySelector('textarea').focus();
      return;
    }
    if (event.target.closest('.mus-cancel')) {
      var _form = event.target.closest('.mus-edit-form');
      _form.hidden = true;
      _form.parentElement.querySelector('.mus-edit-button').hidden = false;
    }
  });
  room.addEventListener('input', function (event) {
    if (event.target.closest('.mus-edit-form')) stopTour();
  });
  window.addEventListener('resize', function () {
    room.querySelectorAll('.mus-slot').forEach(function (slot) {
      var distance = Number(slot.dataset.index) - current;
      slot.style.setProperty('--mus-offset', "".concat(distance * (window.innerWidth < 701 ? 185 : 285), "px"));
    });
  });
  setFrame();
  render();
});
/******/ })()
;