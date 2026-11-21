/******/ (() => { // webpackBootstrap
/*!********************************!*\
  !*** ./resources/js/museum.js ***!
  \********************************/
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
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
  var frameRatio = 725 / 1024;
  function sizeFrame() {
    var frameHeight = Math.round(room.clientHeight * (window.innerWidth < 701 ? 0.45 : 0.44));
    room.style.setProperty('--mus-frame-height', "".concat(frameHeight, "px"));
    room.style.setProperty('--mus-frame-width', "".concat(Math.round(frameHeight * frameRatio), "px"));
  }
  function useDefaultOpening() {
    room.style.setProperty('--mus-it', '8.5%');
    room.style.setProperty('--mus-il', '11%');
    room.style.setProperty('--mus-ir', '11%');
    room.style.setProperty('--mus-ib', '8.5%');
  }
  function setFrame() {
    var frame = new Image();
    frame.onload = function () {
      frameRatio = frame.naturalWidth / frame.naturalHeight;
      room.style.setProperty('--mus-frame-ratio', "".concat(frame.naturalWidth, " / ").concat(frame.naturalHeight));
      sizeFrame();
      try {
        var width = Math.min(500, frame.naturalWidth);
        var height = Math.max(1, Math.round(width / frameRatio));
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
      portrait.alt = "Portrait de ".concat(item.name);
      portrait.loading = index === current ? 'eager' : 'lazy';
      portrait.decoding = 'async';
      portrait.addEventListener('error', function () {
        portrait.hidden = true;
        silhouette.hidden = false;
        silhouette.setAttribute('aria-hidden', 'false');
      }, {
        once: true
      });
      art.appendChild(portrait);
      portrait.src = new URL(item.image_url, window.location.href).href;
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
    if (item.gallery_url) {
      var galleryLink = document.createElement('a');
      galleryLink.className = 'mus-sheet-link';
      galleryLink.href = item.gallery_url;
      galleryLink.textContent = "Sa galerie \xB7 ".concat(item.gallery_count || 0, " images");
      galleryLink.style.marginLeft = '8px';
      plaque.appendChild(galleryLink);
    }
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
    var previousCurrent = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : current;
    if (!paintings.length) return;
    current = Math.max(0, Math.min(paintings.length - 1, current));
    var indexes = [current - 1, current, current + 1].filter(function (index) {
      return index >= 0 && index < paintings.length;
    });
    var fragment = document.createDocumentFragment();
    indexes.forEach(function (index) {
      var slot = createSlot(paintings[index], index);
      var step = window.innerWidth < 701 ? 185 : 285;
      var distance = index - current;
      var startingDistance = index - previousCurrent;
      slot.classList.toggle('mus-slot--active', distance === 0);
      slot.classList.toggle('mus-slot--previous', distance < 0);
      slot.classList.toggle('mus-slot--next', distance > 0);
      slot.style.setProperty('--mus-offset', "".concat(startingDistance * step, "px"));
      slot.style.setProperty('--mus-scale', startingDistance === 0 ? '1' : '0.67');
      slot.style.setProperty('--mus-layer', startingDistance === 0 ? '3' : '1');
      slot.addEventListener('click', function (event) {
        if (!event.target.closest('.mus-art')) return;
        if (index !== current) {
          stopTour();
          go(index);
        } else if (item.gallery_url) {
          window.location.href = item.gallery_url;
        }
      });
      fragment.appendChild(slot);
    });
    stage.replaceChildren(fragment);
    if (!reducedMotion && previousCurrent !== current) {
      // Commit the old positions before moving the frames into their new places.
      stage.offsetWidth;
      window.requestAnimationFrame(function () {
        stage.querySelectorAll('.mus-slot').forEach(function (slot) {
          var distance = Number(slot.dataset.index) - current;
          slot.style.setProperty('--mus-offset', "".concat(distance * (window.innerWidth < 701 ? 185 : 285), "px"));
          slot.style.setProperty('--mus-scale', distance === 0 ? '1' : '0.67');
          slot.style.setProperty('--mus-layer', distance === 0 ? '3' : '1');
        });
      });
    }
    var item = paintings[current];
    updateControls(item);
  }
  function go(index) {
    if (index < 0 || index >= paintings.length || index === current) return;
    var previous = current;
    current = index;
    render(previous);
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
    sizeFrame();
    room.querySelectorAll('.mus-slot').forEach(function (slot) {
      var distance = Number(slot.dataset.index) - current;
      slot.style.setProperty('--mus-offset', "".concat(distance * (window.innerWidth < 701 ? 185 : 285), "px"));
    });
  });
  sizeFrame();
  setFrame();
  render();
});
document.addEventListener('DOMContentLoaded', function () {
  var root = document.querySelector('[data-gallery]');
  if (!root) return;
  var images = JSON.parse(root.dataset.images || '[]');
  var characters = JSON.parse(root.dataset.characters || '[]');
  var viewer = root.querySelector('[data-viewer]');
  var viewerContent = viewer.querySelector('.mus-gallery-view-content');
  var viewerArt = viewer.querySelector('[data-viewer-frame] .mus-art');
  var active = 0;
  var opener = null;
  var token = root.dataset.csrf;
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var esc = function esc(value) {
    return String(value || '').replace(/[&<>"']/g, function (_char) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
      }[_char];
    });
  };
  var request = function request(url, method, data) {
    return fetch(url, {
      method: method,
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: data ? JSON.stringify(data) : undefined
    }).then(/*#__PURE__*/function () {
      var _ref = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee(response) {
        var body;
        return _regenerator().w(function (_context) {
          while (1) switch (_context.n) {
            case 0:
              _context.n = 1;
              return response.json()["catch"](function () {
                return {};
              });
            case 1:
              body = _context.v;
              if (response.ok) {
                _context.n = 2;
                break;
              }
              throw new Error(body.message || 'La modification a échoué.');
            case 2:
              return _context.a(2, body);
          }
        }, _callee);
      }));
      return function (_x) {
        return _ref.apply(this, arguments);
      };
    }());
  };
  root.addEventListener('click', function (event) {
    root.querySelectorAll('[data-mention-picker]').forEach(function (picker) {
      if (!picker.contains(event.target)) picker.querySelector('[data-mention-results]').hidden = true;
    });
  });
  function attachMentionPicker(picker) {
    var _picker$closest;
    var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    if (!picker) return;
    var query = picker.querySelector('[data-mention-query]');
    var results = picker.querySelector('[data-mention-results]');
    var selected = picker.querySelector('[data-mention-selected]');
    var multiple = options.multiple === true;
    var excluded = new Set((options.excluded || []).map(Number));
    var chosen = new Map();
    var submit = (_picker$closest = picker.closest('form')) === null || _picker$closest === void 0 ? void 0 : _picker$closest.querySelector('button[type="submit"]');
    function renderSelected() {
      selected.replaceChildren();
      chosen.forEach(function (person) {
        var chip = document.createElement('span');
        chip.className = 'mus-mention-chip';
        var label = document.createElement('span');
        label.textContent = "@".concat(person.first_name || person.name);
        chip.appendChild(label);
        var value = document.createElement('input');
        value.type = 'hidden';
        value.name = multiple ? 'mentions[]' : 'character_id';
        value.value = String(person.id);
        chip.appendChild(value);
        var remove = document.createElement('button');
        remove.type = 'button';
        remove.textContent = '?';
        remove.setAttribute('aria-label', "Retirer @".concat(person.first_name || person.name));
        remove.addEventListener('click', function () {
          chosen["delete"](Number(person.id));
          renderSelected();
        });
        chip.appendChild(remove);
        selected.appendChild(chip);
      });
      if (submit && !multiple) submit.disabled = chosen.size === 0;
    }
    function showResults() {
      var needle = query.value.replace(/^@/, '').trim().toLocaleLowerCase();
      results.replaceChildren();
      if (!needle) {
        results.hidden = true;
        return;
      }
      var matches = characters.filter(function (person) {
        var id = Number(person.id);
        if (chosen.has(id) || excluded.has(id)) return false;
        var firstName = String(person.first_name || person.name).toLocaleLowerCase();
        return firstName.includes(needle) || String(person.name).toLocaleLowerCase().includes(needle);
      }).slice(0, 8);
      matches.forEach(function (person) {
        var option = document.createElement('button');
        option.type = 'button';
        option.className = 'mus-mention-option';
        option.setAttribute('role', 'option');
        option.textContent = "@".concat(person.first_name || person.name, " ? ").concat(person.name);
        option.addEventListener('click', function () {
          chosen.set(Number(person.id), person);
          query.value = '';
          results.hidden = true;
          renderSelected();
          query.focus();
        });
        results.appendChild(option);
      });
      if (!matches.length) {
        var empty = document.createElement('p');
        empty.className = 'mus-mention-option';
        empty.textContent = 'Aucun personnage correspondant dans ce monde.';
        results.appendChild(empty);
      }
      results.hidden = false;
    }
    query.addEventListener('input', showResults);
    query.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') results.hidden = true;
    });
  }
  function calibrateFrames() {
    document.querySelectorAll('.mus-gallery-frame').forEach(function (node) {
      node.style.setProperty('--mus-frame-ratio', '725 / 1024');
      node.style.setProperty('--mus-il', '13.8%');
      node.style.setProperty('--mus-ir', '13.7%');
      node.style.setProperty('--mus-it', '17.3%');
      node.style.setProperty('--mus-ib', '18.3%');
    });
  }
  function showViewer(index) {
    if (!images.length) return;
    active = (index + images.length) % images.length;
    var image = images[active];
    opener = document.activeElement;
    viewerArt.innerHTML = "<img src=\"".concat(esc(image.url), "\" alt=\"").concat(esc(image.alt), "\">");
    viewerContent.innerHTML = "\n            <h2 id=\"mus-viewer-title\">".concat(esc(image.title), "</h2>\n            <p>Photo de ").concat(esc(image.owner), "</p>\n            ").concat(image.owner_url ? "<p><a href=\"".concat(esc(image.owner_url), "\">Ouvrir la fiche</a></p>") : '', "\n            <textarea maxlength=\"280\" rows=\"3\" data-description ").concat(image.can_manage ? '' : 'readonly', ">").concat(esc(image.description), "</textarea>\n            ").concat(image.can_manage ? '<button type="button" data-save-description>Enregistrer</button>' : '', "\n            <h3>Personnages mentionn\xE9s</h3>\n            <div class=\"mus-gallery-mentions\" data-mentions></div>\n            ").concat(image.can_manage ? '<form data-mention-form><div class="mus-mention-picker" data-mention-picker><label>Ajouter une mention <input type="search" data-mention-query autocomplete="off" placeholder="@prénom" aria-autocomplete="list" aria-label="Rechercher un personnage avec @"></label><div class="mus-mention-results" data-mention-results role="listbox" hidden></div><div class="mus-mention-selected" data-mention-selected aria-live="polite"></div></div><button type="submit" disabled>Mentionner</button><p role="alert" data-mention-error></p></form><button type="button" data-delete>Supprimer l’image</button>' : '');
    var pills = viewerContent.querySelector('[data-mentions]');
    (image.mentions || []).forEach(function (mention) {
      var link = document.createElement('a');
      link.href = mention.url;
      link.textContent = mention.name;
      pills.appendChild(link);
      if (image.can_manage) {
        var remove = document.createElement('button');
        remove.type = 'button';
        remove.textContent = '×';
        remove.setAttribute('aria-label', "Retirer ".concat(mention.name));
        remove.addEventListener('click', function () {
          return request(image.mention_url.replace(/\/mentions$/, "/mentions/".concat(mention.id)), 'DELETE').then(function () {
            image.mentions = image.mentions.filter(function (item) {
              return item.id !== mention.id;
            });
            showViewer(active);
          });
        });
        pills.appendChild(remove);
      }
    });
    var mentionPicker = viewerContent.querySelector('[data-mention-picker]');
    if (mentionPicker) attachMentionPicker(mentionPicker, {
      excluded: [].concat(_toConsumableArray(image.mentions.map(function (mention) {
        return mention.id;
      })), [image.owner_id])
    });
    var save = viewerContent.querySelector('[data-save-description]');
    if (save) save.addEventListener('click', function () {
      return request(image.update_url, 'PUT', {
        description: viewerContent.querySelector('[data-description]').value
      }).then(function (result) {
        image.description = result.description;
        save.textContent = 'Enregistré';
      })["catch"](function (error) {
        save.textContent = error.message;
      });
    });
    var mentionForm = viewerContent.querySelector('[data-mention-form]');
    if (mentionForm) mentionForm.addEventListener('submit', function (event) {
      event.preventDefault();
      var id = Number(new FormData(mentionForm).get('character_id'));
      request(image.mention_url, 'POST', {
        character_id: id
      }).then(function () {
        var person = characters.find(function (item) {
          return item.id === id;
        });
        image.mentions.push({
          id: id,
          name: person.name,
          url: window.location.pathname.replace(/\d+$/, String(id))
        });
        showViewer(active);
      })["catch"](function (error) {
        mentionForm.querySelector('[data-mention-error]').textContent = error.message;
      });
    });
    var deleteButton = viewerContent.querySelector('[data-delete]');
    if (deleteButton) deleteButton.addEventListener('click', function () {
      return request(image.delete_url, 'DELETE').then(function () {
        var _document$querySelect;
        images.splice(active, 1);
        (_document$querySelect = document.querySelector("[data-open-image=\"".concat(image.id, "\"]"))) === null || _document$querySelect === void 0 || (_document$querySelect = _document$querySelect.closest('.mus-gallery-card')) === null || _document$querySelect === void 0 || _document$querySelect.remove();
        if (!images.length) {
          viewer.hidden = true;
          window.location.hash = '';
          return;
        }
        showViewer(Math.min(active, images.length - 1));
      });
    });
    viewer.hidden = false;
    viewer.focus();
    document.body.style.overflow = 'hidden';
    window.location.hash = "image-".concat(image.id);
  }
  function close(dialog) {
    dialog.hidden = true;
    document.body.style.overflow = '';
    if (dialog === viewer) window.history.replaceState({}, '', "".concat(location.pathname).concat(location.search));
    if (opener) opener.focus();
  }
  root.querySelectorAll('[data-open-image]').forEach(function (button) {
    return button.addEventListener('click', function () {
      return showViewer(images.findIndex(function (image) {
        return image.id === Number(button.dataset.openImage);
      }));
    });
  });
  viewer.querySelectorAll('[data-close]').forEach(function (button) {
    return button.addEventListener('click', function () {
      return close(viewer);
    });
  });
  viewer.querySelectorAll('[data-step]').forEach(function (button) {
    return button.addEventListener('click', function () {
      return showViewer(active + Number(button.dataset.step));
    });
  });
  [viewer, root.querySelector('[data-uploader]')].filter(Boolean).forEach(function (dialog) {
    dialog.addEventListener('click', function (event) {
      if (event.target === dialog) close(dialog);
    });
    dialog.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        event.preventDefault();
        close(dialog);
        return;
      }
      if (event.key === 'ArrowLeft' && dialog === viewer) {
        event.preventDefault();
        showViewer(active - 1);
      }
      if (event.key === 'ArrowRight' && dialog === viewer) {
        event.preventDefault();
        showViewer(active + 1);
      }
      if (event.key === 'Tab') {
        var focusable = Array.from(dialog.querySelectorAll('button:not(:disabled),a[href],input,select,textarea')).filter(function (item) {
          return !item.hidden;
        });
        var first = focusable[0],
          last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) {
          event.preventDefault();
          last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
          event.preventDefault();
          first.focus();
        }
      }
    });
  });
  var uploader = root.querySelector('[data-uploader]');
  if (uploader) {
    root.querySelector('[data-open-upload]').addEventListener('click', function () {
      opener = document.activeElement;
      uploader.hidden = false;
      uploader.querySelector('input[name="title"]').focus();
      document.body.style.overflow = 'hidden';
    });
    uploader.querySelector('[data-close]').addEventListener('click', function () {
      return close(uploader);
    });
    attachMentionPicker(uploader.querySelector('[data-mention-picker]'), {
      multiple: true
    });
    uploader.querySelector('form').addEventListener('submit', function (event) {
      event.preventDefault();
      var form = event.currentTarget;
      var button = form.querySelector('button[type="submit"]');
      var error = form.querySelector('[data-error]');
      button.disabled = true;
      button.textContent = 'Envoi en cours?';
      fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json'
        },
        body: new FormData(form)
      }).then(/*#__PURE__*/function () {
        var _ref2 = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee2(response) {
          var result;
          return _regenerator().w(function (_context2) {
            while (1) switch (_context2.n) {
              case 0:
                _context2.n = 1;
                return response.json();
              case 1:
                result = _context2.v;
                if (response.ok) {
                  _context2.n = 2;
                  break;
                }
                throw new Error(result.message || Object.values(result.errors || {}).flat()[0] || 'Impossible d?ajouter cette image.');
              case 2:
                return _context2.a(2, result);
            }
          }, _callee2);
        }));
        return function (_x2) {
          return _ref2.apply(this, arguments);
        };
      }()).then(function () {
        return window.location.reload();
      })["catch"](function (err) {
        error.textContent = err.message;
        button.disabled = false;
        button.textContent = "Ajouter l'image";
      });
    });
  }
  document.querySelectorAll('.mus-gallery-frame').forEach(function (frame) {
    var img = frame.querySelector('img:first-child');
    img.addEventListener('error', function () {
      img.hidden = true;
      frame.classList.add('mus-fr--fallback');
    }, {
      once: true
    });
  });
  calibrateFrames();
  var linkedId = location.hash.match(/^#image-(\d+)$/);
  if (linkedId) {
    var index = images.findIndex(function (image) {
      return image.id === Number(linkedId[1]);
    });
    if (index >= 0) showViewer(index);
  }
});
/******/ })()
;