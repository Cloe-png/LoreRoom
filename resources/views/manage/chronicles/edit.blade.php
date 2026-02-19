@extends('manage.layout')

@section('title', 'Gestion - Editer chronique')
@section('header', 'Editer chronique')

@section('content')
    <style>
        .chronicle-form-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 16px;
            align-items: start;
        }
        .chronicle-preview {
            position: sticky;
            top: 14px;
            padding: 12px;
            border: 1px solid rgba(95, 69, 40, .25);
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(255,255,255,.75), rgba(248,239,226,.7));
        }
        .chronicle-preview h4 {
            margin: 0 0 10px;
            font-size: 1rem;
        }
        .preview-card {
            border-radius: 18px;
            border: 2px solid rgba(54, 39, 21, .26);
            border-left: 6px solid #8f6b3c;
            padding: 10px 12px;
            background: linear-gradient(180deg, #fffefb 0%, #f8efe2 100%);
            box-shadow: 0 8px 16px rgba(0,0,0,.08);
            overflow: hidden;
        }
        .preview-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
        }
        .preview-date { font-weight: 800; color: #5f4220; font-size: .92rem; }
        .preview-badge {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            padding: 3px 8px;
            border-radius: 999px;
            border: 1px solid rgba(95, 73, 45, .4);
            background: rgba(255,255,255,.78);
            color: #2b2012;
        }
        .preview-title {
            margin: 0;
            font-size: 1.04rem;
            line-height: 1.2;
            color: #1f1a14;
        }
        .preview-shell {
            border: 1px solid rgba(78,58,33,.22);
            border-radius: 16px;
            overflow: hidden;
            background: rgba(255,255,255,.45);
        }
        .preview-hero {
            position: relative;
            height: 90px;
            background:
                linear-gradient(180deg, rgba(37,31,22,.38), rgba(37,31,22,.2)),
                var(--preview-hero-image, linear-gradient(120deg, #d7c2a2, #b99261));
            background-size: cover;
            background-position: center;
            border-bottom: 1px solid rgba(70,50,29,.3);
        }
        .preview-hero-label {
            position: absolute;
            left: 10px;
            bottom: 8px;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #f8efe0;
            background: rgba(21,17,13,.46);
            border: 1px solid rgba(255,255,255,.22);
        }
        .preview-middle {
            display: grid;
            grid-template-columns: minmax(0,1fr) 86px;
            gap: 10px;
            padding: 10px 10px 8px;
            border-bottom: 1px solid rgba(70,50,29,.18);
        }
        .preview-summary {
            margin: 7px 0 0;
            color: #463827;
            font-size: .92rem;
            line-height: 1.35;
        }
        .preview-portrait {
            width: 86px;
            height: 86px;
            border-radius: 20px;
            object-fit: cover;
            border: 2px solid rgba(84,63,38,.35);
            box-shadow: 0 5px 12px rgba(0,0,0,.16);
            background: #efe5d5;
        }
        .preview-portrait-fallback {
            width: 86px;
            height: 86px;
            border-radius: 20px;
            border: 2px solid rgba(84,63,38,.35);
            background: linear-gradient(180deg, #f2eadf, #e4d6c1);
            color: #5f4422;
            font-size: 1.5rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .preview-foot {
            position: relative;
            padding: 8px 10px 10px;
            background: rgba(255,255,255,.72);
        }
        .preview-foot::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(255,255,255,.74), rgba(255,255,255,.84)),
                var(--preview-hero-image, linear-gradient(120deg, #ddc8aa, #c09a69));
            background-size: cover;
            background-position: center bottom;
            opacity: .34;
            pointer-events: none;
        }
        .preview-foot > * {
            position: relative;
            z-index: 1;
        }
        @media (max-width: 980px) {
            .chronicle-form-grid { grid-template-columns: 1fr; }
            .chronicle-preview { position: static; }
        }
    </style>
    <section class="panel">
        <form method="POST" action="{{ route('manage.chronicles.update', $chronicle) }}" class="chronicle-form-grid">
            @csrf @method('PUT')
            <div>
                <div class="field">
                    <label>Monde</label>
                    <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
                </div>
                <div class="field">
                    <label>Titre</label>
                    <input id="preview_title_input" type="text" name="title" value="{{ old('title', $chronicle->title) }}" required>
                </div>
                <div class="field">
                    <label>Date événement</label>
                    <input id="preview_date_input" type="date" name="event_date" value="{{ old('event_date', optional($chronicle->event_date)->format('Y-m-d')) }}">
                </div>
                <div class="field">
                    <label>Date de fin</label>
                    <input id="preview_end_date_input" type="date" name="end_date" value="{{ old('end_date', optional($chronicle->end_date)->format('Y-m-d')) }}">
                </div>
                <div class="field">
                    <label>Lieu de l'événement</label>
                    <select id="preview_location_input" name="event_place_id">
                        <option value="">Aucun lieu</option>
                        @foreach($places as $place)
                            <option value="{{ $place->id }}" {{ (string) old('event_place_id', $chronicle->event_place_id) === (string) $place->id ? 'selected' : '' }}>
                                {{ $place->name }}{{ $place->region ? ' - ' . $place->region : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Résumé</label>
                    <textarea id="preview_summary_input" name="summary">{{ old('summary', $chronicle->summary) }}</textarea>
                </div>
                <div class="field">
                    <label>Contenu</label>
                    <textarea name="content">{{ old('content', $chronicle->content) }}</textarea>
                </div>
            @php
                $selectedLinked = collect(old('linked_character_ids', $chronicle->linkedCharacters->pluck('id')->all()))->map(fn ($id) => (int) $id);
            @endphp
                <div class="field">
                    <label>Personnages liés</label>
                    <select id="preview_linked_input" name="linked_character_ids[]" multiple size="8">
                        @foreach($characters as $character)
                            <option
                                value="{{ $character->id }}"
                                data-image-url="{{ $character->image_path ? route('media.show', ['path' => $character->image_path]) : '' }}"
                                {{ $selectedLinked->contains((int) $character->id) ? 'selected' : '' }}
                            >
                                {{ $character->display_name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="muted">Maintiens Ctrl (ou Cmd) pour sélectionner plusieurs personnages.</p>
                </div>
                <div class="stack">
                    <button class="btn" type="submit">Enregistrer</button>
                    <a class="btn secondary" href="{{ route('manage.chronicles.index') }}">Retour</a>
                </div>
            </div>

            <aside class="chronicle-preview">
                <h4>Aperçu fenêtre événement</h4>
                <article class="preview-card">
                    <div class="preview-top">
                        <span id="preview_date" class="preview-date">Date inconnue</span>
                        <span class="preview-badge">Chronique</span>
                    </div>
                    <div id="preview_shell" class="preview-shell">
                        <div class="preview-hero">
                            <span class="preview-hero-label">Chronique</span>
                        </div>
                        <div class="preview-middle">
                            <div>
                                <h5 id="preview_title" class="preview-title">Titre de l'événement</h5>
                                <p id="preview_summary" class="preview-summary">Le résumé apparaîtra ici dans la frise.</p>
                            </div>
                            <div>
                                <img id="preview_portrait_img" class="preview-portrait" alt="Portrait" style="display:none;">
                                <div id="preview_portrait_fallback" class="preview-portrait-fallback">E</div>
                            </div>
                        </div>
                        <div class="preview-foot">
                            <div id="preview_location" class="muted" style="font-size:.82rem;">Lieu: Non renseigné</div>
                        </div>
                    </div>
                </article>
            </aside>
        </form>
    </section>
    <script>
        (function () {
            const titleInput = document.getElementById('preview_title_input');
            const dateInput = document.getElementById('preview_date_input');
            const endDateInput = document.getElementById('preview_end_date_input');
            const locationInput = document.getElementById('preview_location_input');
            const summaryInput = document.getElementById('preview_summary_input');
            const linkedInput = document.getElementById('preview_linked_input');
            const titleOut = document.getElementById('preview_title');
            const dateOut = document.getElementById('preview_date');
            const summaryOut = document.getElementById('preview_summary');
            const locationOut = document.getElementById('preview_location');
            const shell = document.getElementById('preview_shell');
            const portraitImg = document.getElementById('preview_portrait_img');
            const portraitFallback = document.getElementById('preview_portrait_fallback');
            if (!titleInput || !dateInput || !endDateInput || !locationInput || !summaryInput || !linkedInput) return;

            const fmtDate = (value) => {
                if (!value) return 'Date inconnue';
                const p = value.split('-');
                return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : value;
            };
            const fmtRange = (start, end) => {
                if (!start && !end) return 'Date inconnue';
                if (start && end) return `${fmtDate(start)} -> ${fmtDate(end)}`;
                return fmtDate(start || end);
            };

            const refresh = () => {
                const title = titleInput.value.trim();
                const summary = summaryInput.value.trim();
                const location = locationInput.options[locationInput.selectedIndex]?.text || '';
                const selectedOptions = Array.from(linkedInput.selectedOptions);
                const firstSelected = selectedOptions[0] || null;
                const imageUrl = firstSelected ? (firstSelected.dataset.imageUrl || '') : '';
                titleOut.textContent = title || "Titre de l'événement";
                dateOut.textContent = fmtRange(dateInput.value, endDateInput.value);
                summaryOut.textContent = summary || 'Le résumé apparaîtra ici dans la frise.';
                locationOut.textContent = location && location !== 'Aucun lieu' ? `Lieu: ${location}` : 'Lieu: Non renseigné';

                if (imageUrl) {
                    shell.style.setProperty('--preview-hero-image', `url('${imageUrl}')`);
                    portraitImg.src = imageUrl;
                    portraitImg.style.display = '';
                    portraitFallback.style.display = 'none';
                } else {
                    shell.style.removeProperty('--preview-hero-image');
                    portraitImg.removeAttribute('src');
                    portraitImg.style.display = 'none';
                    portraitFallback.style.display = '';
                    const initial = (title || 'E').trim().charAt(0).toUpperCase() || 'E';
                    portraitFallback.textContent = initial;
                }
            };

            ['input', 'change'].forEach(evt => {
                titleInput.addEventListener(evt, refresh);
                dateInput.addEventListener(evt, refresh);
                endDateInput.addEventListener(evt, refresh);
                locationInput.addEventListener(evt, refresh);
                summaryInput.addEventListener(evt, refresh);
                linkedInput.addEventListener(evt, refresh);
            });
            refresh();
        })();
    </script>
@endsection

