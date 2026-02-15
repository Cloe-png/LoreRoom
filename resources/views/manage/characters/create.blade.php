@extends('manage.layout')

@section('title', 'Gestion - Nouveau personnage')
@section('header', 'Nouveau personnage')

@section('content')
    <style>
        .accordion {
            margin: 10px 0 12px;
            border: 1px solid rgba(114, 84, 49, .35);
            border-radius: 10px;
            background: rgba(255,255,255,.18);
            overflow: hidden;
        }
        .accordion > summary {
            cursor: pointer;
            padding: 10px 12px;
            font-family: "Cinzel","Times New Roman",serif;
            color: #5f421f;
            letter-spacing: .04em;
            text-transform: uppercase;
            background: rgba(255,255,255,.3);
            user-select: none;
        }
        .accordion > summary:hover {
            background: rgba(255,255,255,.42);
        }
        .accordion-body {
            padding: 10px 12px 2px;
        }
        .children-picker label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }
        .children-picker input[type="checkbox"] {
            width: auto;
            margin: 0;
            flex: 0 0 auto;
        }
    </style>
    <section class="panel">
        <form method="POST" action="{{ route('manage.characters.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>

            <div class="field">
                <label>Image (portrait principal)</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div class="grid-4">
                <div class="field">
                    <label>Prénom</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required>
                </div>
                <div class="field">
                    <label>Nom</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}">
                </div>
                <div class="field">
                    <label>Alias / surnoms</label>
                    <input type="text" name="aliases" value="{{ old('aliases') }}">
                </div>
                <div class="field">
                    <label>Statut</label>
                    <select name="status" required>
                        <option value="vivant" {{ old('status', 'vivant') === 'vivant' ? 'selected' : '' }}>Vivant</option>
                        <option value="mort" {{ old('status') === 'mort' ? 'selected' : '' }}>Mort</option>
                        <option value="disparu" {{ old('status') === 'disparu' ? 'selected' : '' }}>Disparu</option>
                        <option value="inconnu" {{ old('status') === 'inconnu' ? 'selected' : '' }}>Inconnu</option>
                    </select>
                </div>
            </div>

            <div class="grid-4">
                <div class="field">
                    <label>Genre</label>
                    <select name="gender">
                        <option value="">-</option>
                        <option value="homme" {{ old('gender') === 'homme' ? 'selected' : '' }}>Homme</option>
                        <option value="femme" {{ old('gender') === 'femme' ? 'selected' : '' }}>Femme</option>
                        <option value="autre" {{ old('gender') === 'autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>
                <div class="field">
                    <label>Rôle</label>
                    <input type="text" name="role" value="{{ old('role') }}">
                </div>
                <div class="field">
                    <label>Date de naissance</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}">
                </div>
                <div class="field">
                    <label>Date de mort</label>
                    <input type="date" name="death_date" value="{{ old('death_date') }}">
                </div>
            </div>

            <details class="accordion" open>
                <summary>Famille</summary>
                <div class="accordion-body">
                    <div class="field">
                        <label>Famille (pivot)</label>
                        <input type="text" name="family_name" value="{{ old('family_name') }}" placeholder="Ex: Stark">
                    </div>
            <div class="grid-4">
                <div class="field">
                    <label>Père</label>
                    <select name="father_id">
                        <option value="">Inconnu</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('father_id') == $parent->id ? 'selected' : '' }}>{{ $parent->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Mère</label>
                    <select name="mother_id">
                        <option value="">Inconnue</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('mother_id') == $parent->id ? 'selected' : '' }}>{{ $parent->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Epouse / Epoux</label>
                    <select name="spouse_id">
                        <option value="">Inconnu(e)</option>
                        @foreach($spouses as $spouse)
                            <option value="{{ $spouse->id }}" {{ old('spouse_id') == $spouse->id ? 'selected' : '' }}>{{ $spouse->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Lieu de naissance</label>
                    <select name="birth_place_id">
                        <option value="">Inconnu</option>
                        @foreach($places as $place)
                            <option value="{{ $place->id }}" {{ old('birth_place_id') == $place->id ? 'selected' : '' }}>{{ $place->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Residence actuelle</label>
                    <select name="residence_place_id">
                        <option value="">Inconnue</option>
                        @foreach($places as $place)
                            <option value="{{ $place->id }}" {{ old('residence_place_id') == $place->id ? 'selected' : '' }}>{{ $place->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="stack" style="margin-bottom:12px;">
                <label><input type="checkbox" id="has_children" name="has_children" value="1" {{ old('has_children') ? 'checked' : '' }}> Enfants (oui/non)</label>
                <label><input type="checkbox" id="has_brother_sister" name="has_brother_sister" value="1" {{ old('has_brother_sister') ? 'checked' : '' }}> Frere / soeur (oui/non)</label>
            </div>

            <div id="children-panel" class="panel" style="margin-top:0; margin-bottom:12px; padding:10px; display:none;">
                <div class="grid-4">
                    <div class="field">
                        <label>Liaison parentale</label>
                        <select name="children_link_type">
                            <option value="father" {{ ($childrenLinkType ?? 'father') === 'father' ? 'selected' : '' }}>Père</option>
                            <option value="mother" {{ ($childrenLinkType ?? 'father') === 'mother' ? 'selected' : '' }}>Mère</option>
                        </select>
                    </div>
                    <div class="field" style="grid-column: span 3;">
                        <label>Personnages déjà créés (enfants)</label>
                        @php
                            $selectedChildren = collect($selectedChildrenIds ?? [])->map(fn ($id) => (int) $id)->all();
                        @endphp
                        @if($characters->isEmpty())
                            <p class="muted">Aucun personnage créé pour le moment.</p>
                        @else
                            <div class="children-picker" style="max-height:220px; overflow:auto; border:1px solid rgba(101,74,42,.28); border-radius:8px; background:rgba(255,255,255,.45); padding:8px;">
                                @foreach($characters as $childCandidate)
                                    <label>
                                        <input type="checkbox" name="children_ids[]" value="{{ $childCandidate->id }}" {{ in_array($childCandidate->id, $selectedChildren) ? 'checked' : '' }}>
                                        {{ $childCandidate->display_name }}
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div id="siblings-panel" class="panel" style="margin-top:0; margin-bottom:12px; padding:10px; display:none;">
                @php
                    $selectedFullSiblings = collect($selectedFullSiblingIds ?? [])->map(fn ($id) => (int) $id)->all();
                    $selectedTwinSiblings = collect($selectedTwinSiblingIds ?? [])->map(fn ($id) => (int) $id)->all();
                    $selectedHalfSiblings = collect($selectedHalfSiblingIds ?? [])->map(fn ($id) => (int) $id)->all();
                @endphp
                @if($characters->isEmpty())
                    <p class="muted">Aucun personnage cree pour le moment.</p>
                @else
                    <div class="grid-4">
                        <div class="field" style="grid-column: span 4;">
                            <label>Zone frere / soeur</label>
                            <div class="children-picker" style="max-height:160px; overflow:auto; border:1px solid rgba(101,74,42,.28); border-radius:8px; background:rgba(255,255,255,.45); padding:8px;">
                                @foreach($characters as $siblingCandidate)
                                    <label>
                                        <input type="checkbox" name="sibling_ids_full[]" value="{{ $siblingCandidate->id }}" {{ in_array($siblingCandidate->id, $selectedFullSiblings) ? 'checked' : '' }}>
                                        {{ $siblingCandidate->display_name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="field" style="grid-column: span 4;">
                            <label>Zone jumeau / jumelle</label>
                            <div class="children-picker" style="max-height:160px; overflow:auto; border:1px solid rgba(101,74,42,.28); border-radius:8px; background:rgba(255,255,255,.45); padding:8px;">
                                @foreach($characters as $siblingCandidate)
                                    <label>
                                        <input type="checkbox" name="sibling_ids_twin[]" value="{{ $siblingCandidate->id }}" {{ in_array($siblingCandidate->id, $selectedTwinSiblings) ? 'checked' : '' }}>
                                        {{ $siblingCandidate->display_name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="field" style="grid-column: span 4;">
                            <label>Zone demi-frere / demi-soeur</label>
                            <div class="children-picker" style="max-height:160px; overflow:auto; border:1px solid rgba(101,74,42,.28); border-radius:8px; background:rgba(255,255,255,.45); padding:8px;">
                                @foreach($characters as $siblingCandidate)
                                    <label>
                                        <input type="checkbox" name="sibling_ids_half[]" value="{{ $siblingCandidate->id }}" {{ in_array($siblingCandidate->id, $selectedHalfSiblings) ? 'checked' : '' }}>
                                        {{ $siblingCandidate->display_name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

                </div>
            </details>

            <div class="stack" style="margin-bottom:12px;">
                <label><input type="checkbox" id="has_power" name="has_power" value="1" {{ old('has_power') ? 'checked' : '' }}> Pouvoir actif</label>
                <label><input type="checkbox" name="secrets_is_private" value="1" {{ old('secrets_is_private', 1) ? 'checked' : '' }}> Secrets prives</label>
            </div>

            <div id="power-panel" class="panel" style="margin-top:0; margin-bottom:12px; padding:10px; display:none;">
                <div class="grid-4">
                    <div class="field">
                        <label>Niveau de puissance (1-10)</label>
                        <input type="number" min="1" max="10" name="power_level" value="{{ old('power_level') }}">
                    </div>
                    <div class="field" style="grid-column: span 3;">
                        <label>Pouvoir (si oui, details)</label>
                        <textarea name="power_description">{{ old('power_description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Objectif court terme</label>
                    <textarea name="short_term_goal">{{ old('short_term_goal') }}</textarea>
                </div>
                <div class="field" style="grid-column: span 2;">
                    <label>Objectif long terme</label>
                    <textarea name="long_term_goal">{{ old('long_term_goal') }}</textarea>
                </div>
            </div>

            <div class="field">
                <label>Secrets</label>
                <textarea name="secrets">{{ old('secrets') }}</textarea>
            </div>

            <details class="accordion" open>
                <summary>Apparence</summary>
                <div class="accordion-body">
                    <div class="grid-4">
                        <div class="field"><label>Taille</label><input type="text" name="height" value="{{ old('height') }}"></div>
                        <div class="field"><label>Cheveux</label><input type="text" name="hair_color" value="{{ old('hair_color') }}"></div>
                        <div class="field"><label>Yeux</label><input type="text" name="eye_color" value="{{ old('eye_color') }}"></div>
                    </div>
                    <div class="field"><label>Cicatrices / tatouages / marques</label><textarea name="marks">{{ old('marks') }}</textarea></div>
                    <div class="field"><label>Maniere de s'habiller</label><textarea name="clothing_style">{{ old('clothing_style') }}</textarea></div>
                </div>
            </details>

            <details class="accordion" open>
                <summary>Psychologie</summary>
                <div class="accordion-body">
                    <div class="field"><label>Qualités</label><textarea name="qualities">{{ old('qualities') }}</textarea></div>
                    <div class="field"><label>Défauts</label><textarea name="flaws">{{ old('flaws') }}</textarea></div>
                    <div class="field"><label>Voix</label><textarea name="voice_tics">{{ old('voice_tics') }}</textarea></div>
                    <div class="field"><label>Résumé général</label><textarea name="summary">{{ old('summary') }}</textarea></div>
                </div>
            </details>

            <details class="accordion">
                <summary>Equipements / artefacts</summary>
                <div class="accordion-body">
            <div id="items-list">
                @foreach(($itemRows ?? []) as $i => $item)
                    <div class="panel" data-item-row style="margin-top:10px; padding:10px;">
                        <div class="grid-4">
                            <div class="field" style="grid-column: span 2;"><label>Nom objet</label><input type="text" name="items[{{ $i }}][name]" value="{{ $item['name'] ?? '' }}"></div>
                            <div class="field"><label>Rarete</label>
                                <select name="items[{{ $i }}][rarity]">
                                    <option value="">-</option>
                                    @foreach(['commun','rare','epique','legendaire','mythique'] as $rarity)
                                        <option value="{{ $rarity }}" {{ ($item['rarity'] ?? '') === $rarity ? 'selected' : '' }}>{{ ucfirst($rarity) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field"><label>Notes</label><textarea name="items[{{ $i }}][notes]">{{ $item['notes'] ?? '' }}</textarea></div>
                        </div>
                        <button class="btn danger" type="button" data-remove-item>Retirer</button>
                    </div>
                @endforeach
            </div>
            <div class="stack" style="margin-bottom:10px;"><button class="btn secondary" type="button" id="add-item-btn">Ajouter un objet</button></div>
                </div>
            </details>

            <details class="accordion">
                <summary>Métiers</summary>
                <div class="accordion-body">
            <div id="jobs-list">
                @foreach(($jobRows ?? []) as $i => $job)
                    <div class="panel" data-job-row style="margin-top:10px; padding:10px;">
                        <div class="grid-4">
                            <div class="field" style="grid-column: span 2;"><label>Métier</label><input type="text" name="jobs[{{ $i }}][job_name]" value="{{ $job['job_name'] ?? '' }}"></div>
                            <div class="field"><label>Année début</label><input type="number" min="1" max="9999" name="jobs[{{ $i }}][start_year]" value="{{ $job['start_year'] ?? '' }}"></div>
                            <div class="field"><label>Année fin</label><input type="number" min="1" max="9999" name="jobs[{{ $i }}][end_year]" value="{{ $job['end_year'] ?? '' }}"></div>
                        </div>
                        <div class="field"><label>Notes</label><textarea name="jobs[{{ $i }}][notes]">{{ $job['notes'] ?? '' }}</textarea></div>
                        <button class="btn danger" type="button" data-remove-job>Retirer</button>
                    </div>
                @endforeach
            </div>
            <div class="stack" style="margin-bottom:10px;"><button class="btn secondary" type="button" id="add-job-btn">Ajouter un metier</button></div>
                </div>
            </details>

            <details class="accordion">
                <summary>Timeline perso</summary>
                <div class="accordion-body">
            <div id="events-list">
                @foreach(($eventRows ?? []) as $i => $event)
                    <div class="panel" data-event-row style="margin-top:10px; padding:10px;">
                        <div class="grid-4">
                            <div class="field"><label>Date</label><input type="date" name="events[{{ $i }}][event_date]" value="{{ $event['event_date'] ?? '' }}"></div>
                            <div class="field" style="grid-column: span 3;"><label>Titre événement</label><input type="text" name="events[{{ $i }}][title]" value="{{ $event['title'] ?? '' }}"></div>
                        </div>
                        <div class="field"><label>Details</label><textarea name="events[{{ $i }}][details]">{{ $event['details'] ?? '' }}</textarea></div>
                        <button class="btn danger" type="button" data-remove-event>Retirer</button>
                    </div>
                @endforeach
            </div>
            <div class="stack" style="margin-bottom:10px;"><button class="btn secondary" type="button" id="add-event-btn">Ajouter un événement</button></div>
                </div>
            </details>

            <details class="accordion">
                <summary>Galerie d'images</summary>
                <div class="accordion-body">
            <div id="gallery-list">
                <div class="panel" data-gallery-row style="margin-top:10px; padding:10px;">
                    <div class="grid-4">
                        <div class="field" style="grid-column: span 2;"><label>Image</label><input type="file" name="gallery_images[]" accept="image/*"></div>
                        <div class="field" style="grid-column: span 2;"><label>Légende</label><input type="text" name="gallery_captions[]" value=""></div>
                    </div>
                    <button class="btn danger" type="button" data-remove-gallery>Retirer</button>
                </div>
            </div>
            <div class="stack" style="margin-bottom:10px;"><button class="btn secondary" type="button" id="add-gallery-btn">Ajouter une image</button></div>
                </div>
            </details>

            <details class="accordion">
                <summary>Relations (sphères liées)</summary>
                <div class="accordion-body">
            <div id="relations-list">
                @foreach(($relationRows ?? []) as $i => $row)
                    <div class="panel" data-relation-row style="margin-top:10px; padding:10px;">
                        <div class="grid-4">
                            <div class="field" style="grid-column: span 2;">
                                <label>Lie a</label>
                                <select name="relations[{{ $i }}][to_character_id]">
                                    <option value="">Aucun</option>
                                    @foreach($characters as $linkedCharacter)
                                        <option value="{{ $linkedCharacter->id }}" {{ (string)($row['to_character_id'] ?? '') === (string)$linkedCharacter->id ? 'selected' : '' }}>{{ $linkedCharacter->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field"><label>Type</label><input type="text" name="relations[{{ $i }}][relation_type]" value="{{ $row['relation_type'] ?? '' }}"></div>
                        </div>
                        <div class="grid-4">
                            <div class="field">
                                <label>Sens</label>
                                <select name="relations[{{ $i }}][is_bidirectional]">
                                    <option value="1" {{ (string)($row['is_bidirectional'] ?? '1') === '1' ? 'selected' : '' }}>Bidirectionnelle</option>
                                    <option value="0" {{ (string)($row['is_bidirectional'] ?? '1') === '0' ? 'selected' : '' }}>Unidirectionnelle</option>
                                </select>
                            </div>
                            <div class="field" style="grid-column: span 3;"><label>Description</label><textarea name="relations[{{ $i }}][description]">{{ $row['description'] ?? '' }}</textarea></div>
                        </div>
                        <button class="btn danger" type="button" data-remove-relation>Retirer</button>
                    </div>
                @endforeach
            </div>
            <div class="stack" style="margin-bottom:10px;"><button class="btn secondary" type="button" id="add-relation-btn">Ajouter une relation</button></div>
                </div>
            </details>

            <div class="stack">
                <button class="btn" type="submit">Créer</button>
                <a class="btn secondary" href="{{ route('manage.characters.index') }}">Annuler</a>
            </div>
        </form>
    </section>

    <template id="item-row-template">
        <div class="panel" data-item-row style="margin-top:10px; padding:10px;">
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;"><label>Nom objet</label><input type="text" data-field="name"></div>
                <div class="field"><label>Rarete</label><select data-field="rarity"><option value="">-</option><option value="commun">Commun</option><option value="rare">Rare</option><option value="epique">Epique</option><option value="legendaire">Legendaire</option><option value="mythique">Mythique</option></select></div>
                <div class="field"><label>Notes</label><textarea data-field="notes"></textarea></div>
            </div>
            <button class="btn danger" type="button" data-remove-item>Retirer</button>
        </div>
    </template>

    <template id="event-row-template">
        <div class="panel" data-event-row style="margin-top:10px; padding:10px;">
            <div class="grid-4">
                <div class="field"><label>Date</label><input type="date" data-field="event_date"></div>
                <div class="field" style="grid-column: span 3;"><label>Titre événement</label><input type="text" data-field="title"></div>
            </div>
            <div class="field"><label>Details</label><textarea data-field="details"></textarea></div>
            <button class="btn danger" type="button" data-remove-event>Retirer</button>
        </div>
    </template>

    <template id="job-row-template">
        <div class="panel" data-job-row style="margin-top:10px; padding:10px;">
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;"><label>Métier</label><input type="text" data-field="job_name"></div>
                <div class="field"><label>Année début</label><input type="number" min="1" max="9999" data-field="start_year"></div>
                <div class="field"><label>Année fin</label><input type="number" min="1" max="9999" data-field="end_year"></div>
            </div>
            <div class="field"><label>Notes</label><textarea data-field="notes"></textarea></div>
            <button class="btn danger" type="button" data-remove-job>Retirer</button>
        </div>
    </template>

    <template id="gallery-row-template">
        <div class="panel" data-gallery-row style="margin-top:10px; padding:10px;">
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;"><label>Image</label><input type="file" name="gallery_images[]" accept="image/*"></div>
                <div class="field" style="grid-column: span 2;"><label>Légende</label><input type="text" name="gallery_captions[]" value=""></div>
            </div>
            <button class="btn danger" type="button" data-remove-gallery>Retirer</button>
        </div>
    </template>

    <template id="relation-row-template">
        <div class="panel" data-relation-row style="margin-top:10px; padding:10px;">
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Lie a</label>
                    <select data-field="to_character_id">
                        <option value="">Aucun</option>
                        @foreach($characters as $linkedCharacter)
                            <option value="{{ $linkedCharacter->id }}">{{ $linkedCharacter->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field"><label>Type</label><input type="text" data-field="relation_type"></div>
            </div>
            <div class="grid-4">
                <div class="field"><label>Sens</label><select data-field="is_bidirectional"><option value="1">Bidirectionnelle</option><option value="0">Unidirectionnelle</option></select></div>
                <div class="field" style="grid-column: span 3;"><label>Description</label><textarea data-field="description"></textarea></div>
            </div>
            <button class="btn danger" type="button" data-remove-relation>Retirer</button>
        </div>
    </template>

    <script>
        (function () {
            function bindCollection(listId, addBtnId, tplId, rowAttr, removeAttr, baseName) {
                const list = document.getElementById(listId);
                const addBtn = document.getElementById(addBtnId);
                const tpl = document.getElementById(tplId);
                if (!list || !addBtn || !tpl) return;

                function reindex() {
                    const rows = list.querySelectorAll(`[${rowAttr}]`);
                    rows.forEach((row, i) => {
                        row.querySelectorAll('input,select,textarea').forEach((field) => {
                            const key = field.dataset.field || null;
                            if (!key) return;
                            field.name = `${baseName}[${i}][${key}]`;
                        });
                    });
                }

                function bindRemove() {
                    list.querySelectorAll(`[${removeAttr}]`).forEach((btn) => {
                        btn.onclick = function () {
                            const rows = list.querySelectorAll(`[${rowAttr}]`);
                            if (rows.length <= 1) return;
                            btn.closest(`[${rowAttr}]`).remove();
                            reindex();
                        };
                    });
                }

                addBtn.addEventListener('click', function () {
                    list.appendChild(tpl.content.firstElementChild.cloneNode(true));
                    reindex();
                    bindRemove();
                });

                reindex();
                bindRemove();
            }

            bindCollection('relations-list', 'add-relation-btn', 'relation-row-template', 'data-relation-row', 'data-remove-relation', 'relations');
            bindCollection('items-list', 'add-item-btn', 'item-row-template', 'data-item-row', 'data-remove-item', 'items');
            bindCollection('jobs-list', 'add-job-btn', 'job-row-template', 'data-job-row', 'data-remove-job', 'jobs');
            bindCollection('events-list', 'add-event-btn', 'event-row-template', 'data-event-row', 'data-remove-event', 'events');

            const galleryList = document.getElementById('gallery-list');
            const addGalleryBtn = document.getElementById('add-gallery-btn');
            const galleryTpl = document.getElementById('gallery-row-template');
            if (galleryList && addGalleryBtn && galleryTpl) {
                function bindGalleryRemove() {
                    galleryList.querySelectorAll('[data-remove-gallery]').forEach((btn) => {
                        btn.onclick = function () {
                            const rows = galleryList.querySelectorAll('[data-gallery-row]');
                            if (rows.length <= 1) return;
                            btn.closest('[data-gallery-row]').remove();
                        };
                    });
                }
                addGalleryBtn.addEventListener('click', function () {
                    galleryList.appendChild(galleryTpl.content.firstElementChild.cloneNode(true));
                    bindGalleryRemove();
                });
                bindGalleryRemove();
            }

            const hasChildren = document.getElementById('has_children');
            const childrenPanel = document.getElementById('children-panel');
            const hasBroSis = document.getElementById('has_brother_sister');
            const siblingsPanel = document.getElementById('siblings-panel');
            const hasPower = document.getElementById('has_power');
            const powerPanel = document.getElementById('power-panel');

            function toggleChildrenPanel() {
                if (!hasChildren || !childrenPanel) return;
                childrenPanel.style.display = hasChildren.checked ? 'block' : 'none';
            }
            function togglePowerPanel() {
                if (!hasPower || !powerPanel) return;
                powerPanel.style.display = hasPower.checked ? 'block' : 'none';
            }
            function toggleSiblingsPanel() {
                if (!hasBroSis || !siblingsPanel) return;
                siblingsPanel.style.display = hasBroSis.checked ? 'block' : 'none';
            }

            toggleChildrenPanel();
            toggleSiblingsPanel();
            togglePowerPanel();
            if (hasChildren) hasChildren.addEventListener('change', toggleChildrenPanel);
            if (hasBroSis) hasBroSis.addEventListener('change', toggleSiblingsPanel);
            if (hasPower) hasPower.addEventListener('change', togglePowerPanel);
        })();
    </script>
@endsection

