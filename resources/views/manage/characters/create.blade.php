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

            <div class="field">
                <label>Couleur préférée (relations)</label>
                <input type="color" name="preferred_color" value="{{ old('preferred_color', '#8F6A3A') }}">
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
                    <select name="role" required>
                        @foreach($roleOptions as $roleOption)
                            <option value="{{ $roleOption }}" {{ old('role', 'Personnage secondaire') === $roleOption ? 'selected' : '' }}>{{ $roleOption }}</option>
                        @endforeach
                    </select>
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

            <details class="accordion">
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
                    <label>Ex (un ou plusieurs)</label>
                    @php
                        $selectedEx = collect($selectedExIds ?? [])->map(fn ($id) => (int) $id)->all();
                    @endphp
                    <select name="ex_character_ids[]" multiple size="5">
                        @foreach($spouses as $spouse)
                            <option value="{{ $spouse->id }}" {{ in_array((int) $spouse->id, $selectedEx, true) ? 'selected' : '' }}>{{ $spouse->display_name }}</option>
                        @endforeach
                    </select>
                    <p class="muted">Maintiens Ctrl (ou Cmd) pour selectionner plusieurs ex.</p>
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
                    <label>Résidence actuelle</label>
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
                <label><input type="checkbox" id="has_brother_sister" name="has_brother_sister" value="1" {{ old('has_brother_sister') ? 'checked' : '' }}> Frère / sœur (oui/non)</label>
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
                    <p class="muted">Aucun personnage créé pour le moment.</p>
                @else
                    <div class="grid-4">
                        <div class="field" style="grid-column: span 4;">
                            <label>Zone frère / sœur</label>
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
                            <label>Zone demi-frère / demi-sœur</label>
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
                <label><input type="checkbox" id="has_private_secret" name="secrets_is_private" value="1" {{ old('secrets_is_private', 0) ? 'checked' : '' }}> Secret privé</label>
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

            <div class="field" id="secret-panel" style="display:none;">
                <label>Secret privé</label>
                <textarea name="secrets">{{ old('secrets') }}</textarea>
            </div>

            <details class="accordion">
                <summary>Apparence</summary>
                <div class="accordion-body">
                    <div class="grid-4">
                        <div class="field"><label>Taille</label><input type="text" name="height" value="{{ old('height') }}"></div>
                        <div class="field"><label>Cheveux</label><input type="text" name="hair_color" value="{{ old('hair_color') }}"></div>
                        <div class="field"><label>Yeux</label><input type="text" name="eye_color" value="{{ old('eye_color') }}"></div>
                    </div>
                    <div class="field">
                        <label>Race du personnage</label>
                        <select name="species_ids[]" multiple size="5">
                            @foreach($speciesOptions as $species)
                                <option value="{{ $species->id }}" {{ in_array((int) $species->id, $selectedSpeciesIds ?? [], true) ? 'selected' : '' }}>
                                    {{ $species->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="muted">Liste alimentée avec les races définies dans le Lore.</p>
                    </div>
                    <div class="field"><label>Cicatrices / tatouages / marques</label><textarea name="marks">{{ old('marks') }}</textarea></div>
                    <div class="field"><label>Manière de s'habiller</label><textarea name="clothing_style">{{ old('clothing_style') }}</textarea></div>
                </div>
            </details>

            <details class="accordion">
                <summary>Psychologie</summary>
                <div class="accordion-body">
                    <div class="field"><label>Qualités</label><textarea name="qualities">{{ old('qualities') }}</textarea></div>
                    <div class="field"><label>Défauts</label><textarea name="flaws">{{ old('flaws') }}</textarea></div>
                    <div class="field"><label>Voix (audio téléversé)</label><input type="file" name="voice_audio" accept="audio/*"></div>
                    <div class="field"><label>Lien YouTube (à la place de l'audio)</label><input type="url" name="voice_youtube_url" value="{{ old('voice_youtube_url') }}" placeholder="https://www.youtube.com/watch?v=..."></div>
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
                            <div class="field"><label>Rareté</label>
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
                            <div class="field" style="grid-column: span 2;">
                                <label>Métier existant</label>
                                <select name="jobs[{{ $i }}][job_id]" data-job-select>
                                    <option value="">-</option>
                                    @foreach($jobOptions as $opt)
                                        <option value="{{ $opt->id }}" {{ (string)($job['job_id'] ?? '') === (string)$opt->id ? 'selected' : '' }}>
                                            {{ $opt->name }}{{ $opt->is_default ? ' · défaut' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field" style="grid-column: span 2;">
                                <label>Métier personnalisé</label>
                                <input type="text" name="jobs[{{ $i }}][job_name]" value="{{ $job['job_name'] ?? '' }}" placeholder="Si non présent dans la liste" list="job-name-options">
                                <p class="muted">Cette zone concerne uniquement le métier du personnage.</p>
                            </div>
                            <div class="field"><label>Année début</label><input type="number" min="1" max="9999" name="jobs[{{ $i }}][start_year]" value="{{ $job['start_year'] ?? '' }}"></div>
                            <div class="field"><label>Année fin</label><input type="number" min="1" max="9999" name="jobs[{{ $i }}][end_year]" value="{{ $job['end_year'] ?? '' }}"></div>
                        </div>
                        <div class="field"><label>Notes</label><textarea name="jobs[{{ $i }}][notes]">{{ $job['notes'] ?? '' }}</textarea></div>
                        <button class="btn danger" type="button" data-remove-job>Retirer</button>
                    </div>
                @endforeach
            </div>
            <div class="stack" style="margin-bottom:10px;"><button class="btn secondary" type="button" id="add-job-btn">Ajouter un métier</button></div>
                </div>
            </details>

            <details class="accordion">
                <summary>Factions / groupes</summary>
                <div class="accordion-body">
            <p class="muted">Lie ce personnage à une faction existante, par exemple "Héros".</p>
            <div id="factions-list">
                @foreach(($factionRows ?? []) as $i => $row)
                    <div class="panel" data-faction-row style="margin-top:10px; padding:10px;">
                        <div class="grid-4">
                            <div class="field" style="grid-column: span 2;">
                                <label>Faction</label>
                                <select name="faction_memberships[{{ $i }}][faction_id]">
                                    <option value="">Aucune</option>
                                    @foreach($factionOptions as $factionOption)
                                        <option value="{{ $factionOption->id }}" {{ (string)($row['faction_id'] ?? '') === (string)$factionOption->id ? 'selected' : '' }}>
                                            {{ $factionOption->name }}{{ $factionOption->type ? ' · ' . $factionOption->type : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field" style="grid-column: span 2;">
                                <label>Rôle dans le groupe</label>
                                <select name="faction_memberships[{{ $i }}][role]" data-faction-role-select>
                                    <option value="">-</option>
                                    @foreach(($factionRoleMap[(int) ($row['faction_id'] ?? 0)] ?? []) as $roleName)
                                        <option value="{{ $roleName }}" {{ ($row['role'] ?? '') === $roleName ? 'selected' : '' }}>{{ $roleName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Grade</label>
                                <input type="text" name="faction_memberships[{{ $i }}][grade]" value="{{ $row['grade'] ?? '' }}">
                            </div>
                            <div class="field">
                                <label>Date d'entrée</label>
                                <input type="date" name="faction_memberships[{{ $i }}][joined_at]" value="{{ $row['joined_at'] ?? '' }}">
                            </div>
                            <div class="field">
                                <label>Statut</label>
                                <select name="faction_memberships[{{ $i }}][status]">
                                    <option value="">-</option>
                                    @foreach($factionMemberStatusOptions as $status)
                                        <option value="{{ $status }}" {{ (string)($row['status'] ?? '') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button class="btn danger" type="button" data-remove-faction>Retirer</button>
                    </div>
                @endforeach
            </div>
            <div class="stack" style="margin-bottom:10px;"><button class="btn secondary" type="button" id="add-faction-btn">Ajouter une faction</button></div>
                </div>
            </details>

            <details class="accordion">
                <summary>Parcours scolaire / formation</summary>
                <div class="accordion-body">
            <p class="muted">Cette zone sert uniquement aux études, écoles, académies et diplômes.</p>
            <div id="educations-list">
                @foreach(($educationRows ?? []) as $i => $row)
                    <div class="panel" data-education-row style="margin-top:10px; padding:10px;">
                        <div class="grid-4">
                            <div class="field" style="grid-column: span 2;">
                                <label>Établissement scolaire</label>
                                <select name="educations[{{ $i }}][faction_id]">
                                    <option value="">Aucune</option>
                                    @foreach($factions as $faction)
                                        <option value="{{ $faction->id }}" {{ (string)($row['faction_id'] ?? '') === (string)$faction->id ? 'selected' : '' }}>
                                            {{ $faction->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field" style="grid-column: span 2;">
                                <label>Diplôme / certificat</label>
                                <select name="educations[{{ $i }}][diploma_id]">
                                    <option value="">Aucun</option>
                                    @foreach($diplomas as $diploma)
                                        @php
                                            $label = $diploma->name;
                                            $school = optional($diploma->faction)->name;
                                            if ($school) {
                                                $label .= ' (' . $school . ')';
                                            }
                                            if ($diploma->level) {
                                                $label .= ' - ' . $diploma->level;
                                            }
                                        @endphp
                                        <option value="{{ $diploma->id }}" {{ (string)($row['diploma_id'] ?? '') === (string)$diploma->id ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid-4">
                            <div class="field" style="grid-column: span 2;">
                                <label>Spécialité / domaine d'étude</label>
                                <input type="text" name="educations[{{ $i }}][field]" value="{{ $row['field'] ?? '' }}">
                            </div>
                            <div class="field">
                                <label>Année début</label>
                                <input type="number" min="1" max="9999" name="educations[{{ $i }}][start_year]" value="{{ $row['start_year'] ?? '' }}">
                            </div>
                            <div class="field">
                                <label>Année fin</label>
                                <input type="number" min="1" max="9999" name="educations[{{ $i }}][end_year]" value="{{ $row['end_year'] ?? '' }}">
                            </div>
                        </div>
                        <div class="field">
                            <label>Notes</label>
                            <textarea name="educations[{{ $i }}][notes]">{{ $row['notes'] ?? '' }}</textarea>
                        </div>
                        <button class="btn danger" type="button" data-remove-education>Retirer</button>
                    </div>
                @endforeach
            </div>
            <div class="stack" style="margin-bottom:10px;"><button class="btn secondary" type="button" id="add-education-btn">Ajouter une formation</button></div>
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
                                <label>Lié à</label>
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
                <div class="field"><label>Rareté</label><select data-field="rarity"><option value="">-</option><option value="commun">Commun</option><option value="rare">Rare</option><option value="epique">Épique</option><option value="legendaire">Légendaire</option><option value="mythique">Mythique</option></select></div>
                <div class="field"><label>Notes</label><textarea data-field="notes"></textarea></div>
            </div>
            <button class="btn danger" type="button" data-remove-item>Retirer</button>
        </div>
    </template>

    <template id="job-row-template">
        <div class="panel" data-job-row style="margin-top:10px; padding:10px;">
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Métier (liste)</label>
                    <select data-field="job_id" data-job-select>
                        <option value="">-</option>
                        @foreach($jobOptions as $opt)
                            <option value="{{ $opt->id }}">{{ $opt->name }}{{ $opt->is_default ? ' · défaut' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" style="grid-column: span 2;">
                    <label>Métier (custom)</label>
                    <input type="text" data-field="job_name" placeholder="Si non présent dans la liste" list="job-name-options">
                </div>
                <div class="field"><label>Année début</label><input type="number" min="1" max="9999" data-field="start_year"></div>
                <div class="field"><label>Année fin</label><input type="number" min="1" max="9999" data-field="end_year"></div>
            </div>
            <div class="field"><label>Notes</label><textarea data-field="notes"></textarea></div>
            <button class="btn danger" type="button" data-remove-job>Retirer</button>
        </div>
    </template>

    <template id="education-row-template">
        <div class="panel" data-education-row style="margin-top:10px; padding:10px;">
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>École / Université</label>
                    <select data-field="faction_id">
                        <option value="">Aucune</option>
                        @foreach($factions as $faction)
                            <option value="{{ $faction->id }}">{{ $faction->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" style="grid-column: span 2;">
                    <label>Diplôme</label>
                    <select data-field="diploma_id">
                        <option value="">Aucun</option>
                        @foreach($diplomas as $diploma)
                            @php
                                $label = $diploma->name;
                                $school = optional($diploma->faction)->name;
                                if ($school) {
                                    $label .= ' (' . $school . ')';
                                }
                                if ($diploma->level) {
                                    $label .= ' - ' . $diploma->level;
                                }
                            @endphp
                            <option value="{{ $diploma->id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Spécialité / filière</label>
                    <input type="text" data-field="field">
                </div>
                <div class="field">
                    <label>Année début</label>
                    <input type="number" min="1" max="9999" data-field="start_year">
                </div>
                <div class="field">
                    <label>Année fin</label>
                    <input type="number" min="1" max="9999" data-field="end_year">
                </div>
            </div>
            <div class="field">
                <label>Notes</label>
                <textarea data-field="notes"></textarea>
            </div>
            <button class="btn danger" type="button" data-remove-education>Retirer</button>
        </div>
    </template>

    <template id="faction-row-template">
        <div class="panel" data-faction-row style="margin-top:10px; padding:10px;">
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Faction</label>
                    <select data-field="faction_id">
                        <option value="">Aucune</option>
                        @foreach($factionOptions as $factionOption)
                            <option value="{{ $factionOption->id }}">{{ $factionOption->name }}{{ $factionOption->type ? ' · ' . $factionOption->type : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" style="grid-column: span 2;">
                    <label>Rôle dans le groupe</label>
                    <select data-field="role" data-faction-role-select>
                        <option value="">-</option>
                    </select>
                </div>
                <div class="field">
                    <label>Grade</label>
                    <input type="text" data-field="grade">
                </div>
                <div class="field">
                    <label>Date d'entrée</label>
                    <input type="date" data-field="joined_at">
                </div>
                <div class="field">
                    <label>Statut</label>
                    <select data-field="status">
                        <option value="">-</option>
                        @foreach($factionMemberStatusOptions as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button class="btn danger" type="button" data-remove-faction>Retirer</button>
        </div>
    </template>

    <datalist id="job-name-options">
        @foreach($jobOptions as $opt)
            <option value="{{ $opt->name }}"></option>
        @endforeach
        @foreach($customJobNameOptions ?? [] as $customJobName)
            <option value="{{ $customJobName }}"></option>
        @endforeach
    </datalist>

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
                    <label>Lié à</label>
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
            bindCollection('factions-list', 'add-faction-btn', 'faction-row-template', 'data-faction-row', 'data-remove-faction', 'faction_memberships');
            bindCollection('educations-list', 'add-education-btn', 'education-row-template', 'data-education-row', 'data-remove-education', 'educations');

            const factionRoleMap = @json($factionRoleMap ?? []);

            function refreshFactionRoleOptions() {
                document.querySelectorAll('#factions-list [data-faction-row]').forEach((row, index) => {
                    const factionSelect = row.querySelector('select[name*="[faction_id]"], select[data-field="faction_id"]');
                    const roleSelect = row.querySelector('select[name*="[role]"], select[data-faction-role-select]');
                    if (!factionSelect || !roleSelect) return;

                    const factionId = String(factionSelect.value || '');
                    const roles = factionRoleMap[factionId] || factionRoleMap[Number(factionId)] || [];
                    const current = roleSelect.value || '';
                    roleSelect.innerHTML = '<option value="">-</option>';
                    roles.forEach((role) => {
                        const option = document.createElement('option');
                        option.value = role;
                        option.textContent = role;
                        if (role === current) {
                            option.selected = true;
                        }
                        roleSelect.appendChild(option);
                    });
                    if (current && !roles.includes(current)) {
                        const option = document.createElement('option');
                        option.value = current;
                        option.textContent = current + ' (existant)';
                        option.selected = true;
                        roleSelect.appendChild(option);
                    }
                });
            }

            document.addEventListener('change', function (event) {
                const target = event.target;
                if (!target || !target.matches('[data-job-select]')) return;
                const panel = target.closest('.panel');
                const nameInput = panel ? panel.querySelector('input[name*="[job_name]"], input[data-field="job_name"]') : null;
                const label = target.options[target.selectedIndex]?.textContent || '';
                if (nameInput && label && label !== '-') {
                    nameInput.value = label.replace(' · défaut', '').trim();
                }
            });
            document.addEventListener('change', function (event) {
                const target = event.target;
                if (target && target.matches('#factions-list select[name*="[faction_id]"], #factions-list select[data-field="faction_id"]')) {
                    refreshFactionRoleOptions();
                }
            });
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
            const hasPrivateSecret = document.getElementById('has_private_secret');
            const secretPanel = document.getElementById('secret-panel');

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
            function toggleSecretPanel() {
                if (!hasPrivateSecret || !secretPanel) return;
                secretPanel.style.display = hasPrivateSecret.checked ? 'block' : 'none';
            }

            toggleChildrenPanel();
            toggleSiblingsPanel();
            togglePowerPanel();
            toggleSecretPanel();
            if (hasChildren) hasChildren.addEventListener('change', toggleChildrenPanel);
            if (hasBroSis) hasBroSis.addEventListener('change', toggleSiblingsPanel);
            if (hasPower) hasPower.addEventListener('change', togglePowerPanel);
            if (hasPrivateSecret) hasPrivateSecret.addEventListener('change', toggleSecretPanel);
            refreshFactionRoleOptions();
        })();
    </script>
@endsection


