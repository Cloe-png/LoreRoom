document.addEventListener('DOMContentLoaded', () => {
    const room = document.querySelector('.mus-room');
    if (!room) return;

    const paintings = JSON.parse(room.querySelector('[data-paintings]').textContent || '[]');
    const stage = room.querySelector('[data-stage]');
    const previousButton = room.querySelector('[data-previous]');
    const nextButton = room.querySelector('[data-next]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let current = Number(room.dataset.activeIndex || 0);
    let tourTimer = null;
    let pointerStart = null;

    function useDefaultOpening() {
        room.style.setProperty('--mus-it', '8.5%');
        room.style.setProperty('--mus-il', '11%');
        room.style.setProperty('--mus-ir', '11%');
        room.style.setProperty('--mus-ib', '8.5%');
    }

    function setFrame() {
        const frame = new Image();
        frame.onload = () => {
            const ratio = frame.naturalWidth / frame.naturalHeight;
            room.style.setProperty('--mus-frame-ratio', `${frame.naturalWidth} / ${frame.naturalHeight}`);

            try {
                const width = Math.min(500, frame.naturalWidth);
                const height = Math.max(1, Math.round(width / ratio));
                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const context = canvas.getContext('2d', { willReadFrequently: true });
                if (!context) throw new Error('Canvas unavailable');
                context.drawImage(frame, 0, 0, width, height);

                const pixels = context.getImageData(0, 0, width, height).data;
                const alphaAt = (x, y) => pixels[(y * width + x) * 4 + 3];
                const centerX = Math.floor(width / 2);
                const centerY = Math.floor(height / 2);
                if (alphaAt(centerX, centerY) >= 128) throw new Error('Opaque frame center');

                let left = centerX;
                let right = centerX;
                let top = centerY;
                let bottom = centerY;
                while (left > 0 && alphaAt(left, centerY) < 128) left -= 1;
                while (right < width - 1 && alphaAt(right, centerY) < 128) right += 1;
                while (top > 0 && alphaAt(centerX, top) < 128) top -= 1;
                while (bottom < height - 1 && alphaAt(centerX, bottom) < 128) bottom += 1;

                const margin = (value) => `${Math.max(0, value - 0.5).toFixed(2)}%`;
                room.style.setProperty('--mus-il', margin((left / width) * 100));
                room.style.setProperty('--mus-ir', margin(((width - right) / width) * 100));
                room.style.setProperty('--mus-it', margin((top / height) * 100));
                room.style.setProperty('--mus-ib', margin(((height - bottom) / height) * 100));
            } catch (error) {
                useDefaultOpening();
            }
        };
        frame.onerror = () => useDefaultOpening();
        frame.src = room.dataset.frameUrl;
    }

    function createSlot(item, index) {
        const slot = document.createElement('article');
        slot.className = 'mus-slot';
        slot.dataset.index = String(index);
        slot.setAttribute('role', 'group');
        slot.setAttribute('aria-roledescription', 'diapositive');
        slot.setAttribute('aria-label', `${item.name}, ${index + 1} sur ${paintings.length}`);

        const frame = document.createElement('figure');
        frame.className = 'mus-fr';
        const art = document.createElement('button');
        art.className = 'mus-art';
        art.type = 'button';
        art.setAttribute('aria-label', `Afficher ${item.name}`);

        const silhouette = document.createElement('span');
        silhouette.className = 'mus-silhouette';
        silhouette.textContent = item.initials || '?';
        silhouette.hidden = Boolean(item.image_url);
        silhouette.setAttribute('aria-hidden', String(Boolean(item.image_url)));
        art.appendChild(silhouette);

        if (item.image_url) {
            const portrait = document.createElement('img');
            portrait.className = 'mus-portrait';
            portrait.src = item.image_url;
            portrait.alt = `Portrait de ${item.name}`;
            portrait.loading = index === current ? 'eager' : 'lazy';
            portrait.addEventListener('error', () => {
                portrait.hidden = true;
                silhouette.hidden = false;
                silhouette.setAttribute('aria-hidden', 'false');
            }, { once: true });
            art.appendChild(portrait);
        }

        const frameImage = document.createElement('img');
        frameImage.className = 'mus-cd';
        frameImage.src = room.dataset.frameUrl;
        frameImage.alt = '';
        frameImage.setAttribute('aria-hidden', 'true');
        frameImage.draggable = false;
        frameImage.addEventListener('error', () => frame.classList.add('mus-fr--fallback'), { once: true });

        frame.append(art, frameImage);

        const plaque = document.createElement('section');
        plaque.className = 'mus-plaque';
        const name = document.createElement('h2');
        name.className = 'mus-name';
        name.textContent = item.name;
        const dates = [item.birth_year, item.death_year].filter(Boolean).join(' — ');
        const details = document.createElement('p');
        details.className = 'mus-details';
        details.textContent = `Salle des ${item.room || 'Inclassables'}${dates ? ` · ${dates}` : ''}`;
        const description = document.createElement('p');
        description.className = 'mus-description';
        description.textContent = item.description || 'Aucune description pour l’instant.';
        plaque.append(name, details, description);

        if (item.can_edit) {
            const editButton = document.createElement('button');
            editButton.className = 'mus-edit-button';
            editButton.type = 'button';
            editButton.textContent = 'Écrire / Modifier la description';
            plaque.appendChild(editButton);

            const form = document.createElement('form');
            form.className = 'mus-edit-form';
            form.method = 'POST';
            form.action = item.update_url;
            form.hidden = true;
            const token = document.createElement('input');
            token.type = 'hidden'; token.name = '_token'; token.value = room.dataset.csrfToken;
            const method = document.createElement('input');
            method.type = 'hidden'; method.name = '_method'; method.value = 'PUT';
            const descriptionOnly = document.createElement('input');
            descriptionOnly.type = 'hidden'; descriptionOnly.name = 'gallery_description_only'; descriptionOnly.value = '1';
            const label = document.createElement('label');
            label.textContent = 'Description du portrait';
            const input = document.createElement('textarea');
            input.name = 'portrait_description';
            input.maxLength = 280;
            input.rows = 3;
            input.value = item.description || '';
            label.appendChild(input);
            const actions = document.createElement('div');
            actions.className = 'mus-edit-actions';
            const save = document.createElement('button');
            save.className = 'mus-save'; save.type = 'submit'; save.textContent = 'Enregistrer';
            const cancel = document.createElement('button');
            cancel.className = 'mus-cancel'; cancel.type = 'button'; cancel.textContent = 'Annuler';
            actions.append(save, cancel);
            form.append(token, method, descriptionOnly, label, actions);
            plaque.appendChild(form);
        }

        const link = document.createElement('a');
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
        const button = room.querySelector('[data-tour]');
        if (button) {
            button.textContent = 'Visite guidée';
            button.setAttribute('aria-pressed', 'false');
        }
    }

    function updateControls(item) {
        previousButton.disabled = current === 0;
        nextButton.disabled = current >= paintings.length - 1;
        room.querySelector('[data-counter]').textContent = `${current + 1} / ${paintings.length}`;
        room.querySelectorAll('[data-jump]').forEach((button) => {
            if (Number(button.dataset.jump) === current) button.setAttribute('aria-current', 'true');
            else button.removeAttribute('aria-current');
        });
        room.querySelector('[data-live]').textContent = `${item.name}, ${current + 1} sur ${paintings.length}. ${item.description || ''}`;
        const url = new URL(window.location.href);
        url.searchParams.set('p', item.id);
        window.history.replaceState({}, '', url);
        room.dataset.activeIndex = String(current);
    }

    function render() {
        if (!paintings.length) return;
        current = Math.max(0, Math.min(paintings.length - 1, current));
        const indexes = [current - 1, current, current + 1].filter((index) => index >= 0 && index < paintings.length);
        const fragment = document.createDocumentFragment();
        indexes.forEach((index) => {
            const slot = createSlot(paintings[index], index);
            const distance = index - current;
            slot.classList.toggle('mus-slot--active', distance === 0);
            slot.classList.toggle('mus-slot--previous', distance < 0);
            slot.classList.toggle('mus-slot--next', distance > 0);
            slot.style.setProperty('--mus-offset', `${distance * (window.innerWidth < 701 ? 185 : 285)}px`);
            slot.style.setProperty('--mus-scale', distance === 0 ? '1' : '0.67');
            slot.style.setProperty('--mus-layer', distance === 0 ? '3' : '1');
            slot.addEventListener('click', (event) => {
                if (!event.target.closest('.mus-art')) return;
                if (index !== current) { stopTour(); go(index); }
            });
            fragment.appendChild(slot);
        });
        stage.replaceChildren(fragment);
        const item = paintings[current];
        updateControls(item);
    }

    function go(index) {
        if (index < 0 || index >= paintings.length || index === current) return;
        current = index;
        render();
    }

    previousButton.addEventListener('click', () => { stopTour(); go(current - 1); });
    nextButton.addEventListener('click', () => { stopTour(); go(current + 1); });
    room.querySelectorAll('[data-jump]').forEach((button) => {
        button.addEventListener('click', () => { stopTour(); go(Number(button.dataset.jump)); });
    });

    room.addEventListener('keydown', (event) => {
        if (event.target.matches('input, textarea, select, [contenteditable="true"]')) return;
        if (event.key === 'ArrowLeft') { event.preventDefault(); stopTour(); go(current - 1); }
        if (event.key === 'ArrowRight') { event.preventDefault(); stopTour(); go(current + 1); }
        if (event.key === 'Home') { event.preventDefault(); stopTour(); go(0); }
        if (event.key === 'End') { event.preventDefault(); stopTour(); go(paintings.length - 1); }
    });

    if (!reducedMotion) {
        room.addEventListener('pointerdown', (event) => {
            if (event.target.closest('button, a, input, textarea')) return;
            pointerStart = event.clientX;
        });
        room.addEventListener('pointerup', (event) => {
            if (pointerStart === null) return;
            const delta = event.clientX - pointerStart;
            pointerStart = null;
            if (Math.abs(delta) >= 50) { stopTour(); go(current + (delta < 0 ? 1 : -1)); }
        });
        room.addEventListener('pointercancel', () => { pointerStart = null; });
    }

    const tourButton = room.querySelector('[data-tour]');
    if (paintings.length === 1) {
        room.querySelector('[data-miniframes]').hidden = true;
        tourButton.hidden = true;
    } else if (reducedMotion) {
        tourButton.hidden = true;
    } else {
        tourButton.addEventListener('click', () => {
            if (tourTimer) { stopTour(); return; }
            tourButton.textContent = 'Arrêter la visite';
            tourButton.setAttribute('aria-pressed', 'true');
            tourTimer = window.setInterval(() => go((current + 1) % paintings.length), 5500);
        });
    }

    room.addEventListener('click', (event) => {
        const edit = event.target.closest('.mus-edit-button');
        if (edit) {
            stopTour();
            const form = edit.parentElement.querySelector('.mus-edit-form');
            form.hidden = false;
            edit.hidden = true;
            form.querySelector('textarea').focus();
            return;
        }
        if (event.target.closest('.mus-cancel')) {
            const form = event.target.closest('.mus-edit-form');
            form.hidden = true;
            form.parentElement.querySelector('.mus-edit-button').hidden = false;
        }
    });
    room.addEventListener('input', (event) => {
        if (event.target.closest('.mus-edit-form')) stopTour();
    });
    window.addEventListener('resize', () => {
        room.querySelectorAll('.mus-slot').forEach((slot) => {
            const distance = Number(slot.dataset.index) - current;
            slot.style.setProperty('--mus-offset', `${distance * (window.innerWidth < 701 ? 185 : 285)}px`);
        });
    });

    setFrame();
    render();
});
