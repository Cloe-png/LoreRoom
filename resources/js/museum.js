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
    let frameRatio = 725 / 1024;

    function sizeFrame() {
        const frameHeight = Math.round(room.clientHeight * (window.innerWidth < 701 ? 0.45 : 0.44));
        room.style.setProperty('--mus-frame-height', `${frameHeight}px`);
        room.style.setProperty('--mus-frame-width', `${Math.round(frameHeight * frameRatio)}px`);
    }

    function useDefaultOpening() {
        room.style.setProperty('--mus-it', '8.5%');
        room.style.setProperty('--mus-il', '11%');
        room.style.setProperty('--mus-ir', '11%');
        room.style.setProperty('--mus-ib', '8.5%');
    }

    function setFrame() {
        const frame = new Image();
        frame.onload = () => {
            frameRatio = frame.naturalWidth / frame.naturalHeight;
            room.style.setProperty('--mus-frame-ratio', `${frame.naturalWidth} / ${frame.naturalHeight}`);
            sizeFrame();

            try {
                const width = Math.min(500, frame.naturalWidth);
                const height = Math.max(1, Math.round(width / frameRatio));
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
            portrait.alt = `Portrait de ${item.name}`;
            portrait.loading = index === current ? 'eager' : 'lazy';
            portrait.decoding = 'async';
            portrait.addEventListener('error', () => {
                portrait.hidden = true;
                silhouette.hidden = false;
                silhouette.setAttribute('aria-hidden', 'false');
            }, { once: true });
            art.appendChild(portrait);
            portrait.src = new URL(item.image_url, window.location.href).href;
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
        if (item.gallery_url) {
            const galleryLink = document.createElement('a');
            galleryLink.className = 'mus-sheet-link';
            galleryLink.href = item.gallery_url;
            galleryLink.textContent = `Sa galerie · ${item.gallery_count || 0} images`;
            galleryLink.style.marginLeft = '8px';
            plaque.appendChild(galleryLink);
        }
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

    function render(previousCurrent = current) {
        if (!paintings.length) return;
        current = Math.max(0, Math.min(paintings.length - 1, current));
        const indexes = [current - 1, current, current + 1].filter((index) => index >= 0 && index < paintings.length);
        const fragment = document.createDocumentFragment();
        indexes.forEach((index) => {
            const slot = createSlot(paintings[index], index);
            const step = window.innerWidth < 701 ? 185 : 285;
            const distance = index - current;
            const startingDistance = index - previousCurrent;
            slot.classList.toggle('mus-slot--active', distance === 0);
            slot.classList.toggle('mus-slot--previous', distance < 0);
            slot.classList.toggle('mus-slot--next', distance > 0);
            slot.style.setProperty('--mus-offset', `${startingDistance * step}px`);
            slot.style.setProperty('--mus-scale', startingDistance === 0 ? '1' : '0.67');
            slot.style.setProperty('--mus-layer', startingDistance === 0 ? '3' : '1');
            slot.addEventListener('click', (event) => {
                if (!event.target.closest('.mus-art')) return;
                if (index !== current) { stopTour(); go(index); }
                else if (item.gallery_url) { window.location.href = item.gallery_url; }
            });
            fragment.appendChild(slot);
        });
        stage.replaceChildren(fragment);
        if (!reducedMotion && previousCurrent !== current) {
            // Commit the old positions before moving the frames into their new places.
            stage.offsetWidth;
            window.requestAnimationFrame(() => {
                stage.querySelectorAll('.mus-slot').forEach((slot) => {
                    const distance = Number(slot.dataset.index) - current;
                    slot.style.setProperty('--mus-offset', `${distance * (window.innerWidth < 701 ? 185 : 285)}px`);
                    slot.style.setProperty('--mus-scale', distance === 0 ? '1' : '0.67');
                    slot.style.setProperty('--mus-layer', distance === 0 ? '3' : '1');
                });
            });
        }
        const item = paintings[current];
        updateControls(item);
    }

    function go(index) {
        if (index < 0 || index >= paintings.length || index === current) return;
        const previous = current;
        current = index;
        render(previous);
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
        sizeFrame();
        room.querySelectorAll('.mus-slot').forEach((slot) => {
            const distance = Number(slot.dataset.index) - current;
            slot.style.setProperty('--mus-offset', `${distance * (window.innerWidth < 701 ? 185 : 285)}px`);
        });
    });

    sizeFrame();
    setFrame();
    render();
});

document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-gallery]');
    if (!root) return;
    let images = JSON.parse(root.dataset.images || '[]');
    const characters = JSON.parse(root.dataset.characters || '[]');
    const viewer = root.querySelector('[data-viewer]');
    const viewerContent = viewer.querySelector('.mus-gallery-view-content');
    const viewerArt = viewer.querySelector('[data-viewer-frame] .mus-art');
    let active = 0;
    let opener = null;
    const token = root.dataset.csrf;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const esc = (value) => String(value || '').replace(/[&<>"']/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
    const request = (url, method, data) => fetch(url, {
        method, headers: {'X-CSRF-TOKEN': token, 'Accept':'application/json', 'Content-Type':'application/json'},
        body: data ? JSON.stringify(data) : undefined,
    }).then(async (response) => {
        const body = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(body.message || 'La modification a échoué.');
        return body;
    });
    root.addEventListener('click', (event) => {
        root.querySelectorAll('[data-mention-picker]').forEach((picker) => {
            if (!picker.contains(event.target)) picker.querySelector('[data-mention-results]').hidden = true;
        });
    });

    function attachMentionPicker(picker, options = {}) {
        if (!picker) return;
        const query = picker.querySelector('[data-mention-query]');
        const results = picker.querySelector('[data-mention-results]');
        const selected = picker.querySelector('[data-mention-selected]');
        const multiple = options.multiple === true;
        const excluded = new Set((options.excluded || []).map(Number));
        const chosen = new Map();
        const submit = picker.closest('form')?.querySelector('button[type="submit"]');

        function renderSelected() {
            selected.replaceChildren();
            chosen.forEach((person) => {
                const chip = document.createElement('span');
                chip.className = 'mus-mention-chip';
                const label = document.createElement('span');
                label.textContent = `@${person.first_name || person.name}`;
                chip.appendChild(label);
                const value = document.createElement('input');
                value.type = 'hidden';
                value.name = multiple ? 'mentions[]' : 'character_id';
                value.value = String(person.id);
                chip.appendChild(value);
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.textContent = '?';
                remove.setAttribute('aria-label', `Retirer @${person.first_name || person.name}`);
                remove.addEventListener('click', () => { chosen.delete(Number(person.id)); renderSelected(); });
                chip.appendChild(remove);
                selected.appendChild(chip);
            });
            if (submit && !multiple) submit.disabled = chosen.size === 0;
        }

        function showResults() {
            const needle = query.value.replace(/^@/, '').trim().toLocaleLowerCase();
            results.replaceChildren();
            if (!needle) { results.hidden = true; return; }
            const matches = characters.filter((person) => {
                const id = Number(person.id);
                if (chosen.has(id) || excluded.has(id)) return false;
                const firstName = String(person.first_name || person.name).toLocaleLowerCase();
                return firstName.includes(needle) || String(person.name).toLocaleLowerCase().includes(needle);
            }).slice(0, 8);
            matches.forEach((person) => {
                const option = document.createElement('button');
                option.type = 'button';
                option.className = 'mus-mention-option';
                option.setAttribute('role', 'option');
                option.textContent = `@${person.first_name || person.name} ? ${person.name}`;
                option.addEventListener('click', () => {
                    chosen.set(Number(person.id), person);
                    query.value = '';
                    results.hidden = true;
                    renderSelected();
                    query.focus();
                });
                results.appendChild(option);
            });
            if (!matches.length) {
                const empty = document.createElement('p');
                empty.className = 'mus-mention-option';
                empty.textContent = 'Aucun personnage correspondant dans ce monde.';
                results.appendChild(empty);
            }
            results.hidden = false;
        }

        query.addEventListener('input', showResults);
        query.addEventListener('keydown', (event) => { if (event.key === 'Escape') results.hidden = true; });
    }
    function calibrateFrames() {
        document.querySelectorAll('.mus-gallery-frame').forEach((node) => {
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
        const image = images[active]; opener = document.activeElement;
        viewerArt.innerHTML = `<img src="${esc(image.url)}" alt="${esc(image.alt)}">`;
        viewerContent.innerHTML = `
            <h2 id="mus-viewer-title">${esc(image.title)}</h2>
            <p>Photo de ${esc(image.owner)}</p>
            ${image.owner_url ? `<p><a href="${esc(image.owner_url)}">Ouvrir la fiche</a></p>` : ''}
            <textarea maxlength="280" rows="3" data-description ${image.can_manage ? '' : 'readonly'}>${esc(image.description)}</textarea>
            ${image.can_manage ? '<button type="button" data-save-description>Enregistrer</button>' : ''}
            <h3>Personnages mentionnés</h3>
            <div class="mus-gallery-mentions" data-mentions></div>
            ${image.can_manage ? '<form data-mention-form><div class="mus-mention-picker" data-mention-picker><label>Ajouter une mention <input type="search" data-mention-query autocomplete="off" placeholder="@prénom" aria-autocomplete="list" aria-label="Rechercher un personnage avec @"></label><div class="mus-mention-results" data-mention-results role="listbox" hidden></div><div class="mus-mention-selected" data-mention-selected aria-live="polite"></div></div><button type="submit" disabled>Mentionner</button><p role="alert" data-mention-error></p></form><button type="button" data-delete>Supprimer l’image</button>' : ''}`;
        const pills = viewerContent.querySelector('[data-mentions]');
        (image.mentions || []).forEach((mention) => {
            const link = document.createElement('a'); link.href = mention.url; link.textContent = mention.name; pills.appendChild(link);
            if (image.can_manage) { const remove = document.createElement('button'); remove.type='button'; remove.textContent='×'; remove.setAttribute('aria-label',`Retirer ${mention.name}`); remove.addEventListener('click', () => request(image.mention_url.replace(/\/mentions$/, `/mentions/${mention.id}`), 'DELETE').then(() => {image.mentions=image.mentions.filter((item)=>item.id!==mention.id);showViewer(active);})); pills.appendChild(remove); }
        });
        const mentionPicker = viewerContent.querySelector('[data-mention-picker]');
        if (mentionPicker) attachMentionPicker(mentionPicker, {excluded: [...image.mentions.map((mention) => mention.id), image.owner_id]});
        const save = viewerContent.querySelector('[data-save-description]');
        if (save) save.addEventListener('click', () => request(image.update_url,'PUT',{description:viewerContent.querySelector('[data-description]').value}).then((result)=>{image.description=result.description;save.textContent='Enregistré';}).catch((error)=>{save.textContent=error.message;}));
        const mentionForm = viewerContent.querySelector('[data-mention-form]');
        if (mentionForm) mentionForm.addEventListener('submit',(event)=>{event.preventDefault();const id=Number(new FormData(mentionForm).get('character_id'));request(image.mention_url,'POST',{character_id:id}).then(()=>{const person=characters.find((item)=>item.id===id);image.mentions.push({id,name:person.name,url:window.location.pathname.replace(/\d+$/,String(id))});showViewer(active);}).catch((error)=>{mentionForm.querySelector('[data-mention-error]').textContent=error.message;});});
        const deleteButton = viewerContent.querySelector('[data-delete]');
        if (deleteButton) deleteButton.addEventListener('click',()=>request(image.delete_url,'DELETE').then(()=>{images.splice(active,1);document.querySelector(`[data-open-image="${image.id}"]`)?.closest('.mus-gallery-card')?.remove();if(!images.length){viewer.hidden=true;window.location.hash='';return;}showViewer(Math.min(active,images.length-1));}));
        viewer.hidden = false; viewer.focus(); document.body.style.overflow='hidden'; window.location.hash=`image-${image.id}`;
    }
    function close(dialog) { dialog.hidden=true; document.body.style.overflow=''; if(dialog===viewer)window.history.replaceState({},'',`${location.pathname}${location.search}`); if(opener)opener.focus(); }

    root.querySelectorAll('[data-open-image]').forEach((button)=>button.addEventListener('click',()=>showViewer(images.findIndex((image)=>image.id===Number(button.dataset.openImage)))));
    viewer.querySelectorAll('[data-close]').forEach((button)=>button.addEventListener('click',()=>close(viewer)));
    viewer.querySelectorAll('[data-step]').forEach((button)=>button.addEventListener('click',()=>showViewer(active+Number(button.dataset.step))));
    [viewer,root.querySelector('[data-uploader]')].filter(Boolean).forEach((dialog)=>{
        dialog.addEventListener('click',(event)=>{if(event.target===dialog)close(dialog);});
        dialog.addEventListener('keydown',(event)=>{
            if(event.key==='Escape'){event.preventDefault();close(dialog);return;}
            if(event.key==='ArrowLeft'&&dialog===viewer){event.preventDefault();showViewer(active-1);}
            if(event.key==='ArrowRight'&&dialog===viewer){event.preventDefault();showViewer(active+1);}
            if(event.key==='Tab'){const focusable=Array.from(dialog.querySelectorAll('button:not(:disabled),a[href],input,select,textarea')).filter((item)=>!item.hidden);const first=focusable[0],last=focusable[focusable.length-1];if(event.shiftKey&&document.activeElement===first){event.preventDefault();last.focus();}else if(!event.shiftKey&&document.activeElement===last){event.preventDefault();first.focus();}}
        });
    });
    const uploader=root.querySelector('[data-uploader]');
    if (uploader) {
        root.querySelector('[data-open-upload]').addEventListener('click', () => { opener = document.activeElement; uploader.hidden = false; uploader.querySelector('input[name="title"]').focus(); document.body.style.overflow = 'hidden'; });
        uploader.querySelector('[data-close]').addEventListener('click', () => close(uploader));
        attachMentionPicker(uploader.querySelector('[data-mention-picker]'), {multiple: true});
        uploader.querySelector('form').addEventListener('submit', (event) => {
            event.preventDefault();
            const form = event.currentTarget;
            const button = form.querySelector('button[type="submit"]');
            const error = form.querySelector('[data-error]');
            button.disabled = true;
            button.textContent = 'Envoi en cours?';
            fetch(form.action, {method: 'POST', headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'}, body: new FormData(form)})
                .then(async (response) => { const result = await response.json(); if (!response.ok) throw new Error(result.message || Object.values(result.errors || {}).flat()[0] || 'Impossible d?ajouter cette image.'); return result; })
                .then(() => window.location.reload())
                .catch((err) => { error.textContent = err.message; button.disabled = false; button.textContent = "Ajouter l'image"; });
        });
    }
    document.querySelectorAll('.mus-gallery-frame').forEach((frame)=>{const img=frame.querySelector('img:first-child');img.addEventListener('error',()=>{img.hidden=true;frame.classList.add('mus-fr--fallback');},{once:true});});
    calibrateFrames();
    const linkedId=location.hash.match(/^#image-(\d+)$/);if(linkedId){const index=images.findIndex((image)=>image.id===Number(linkedId[1]));if(index>=0)showViewer(index);}
});
