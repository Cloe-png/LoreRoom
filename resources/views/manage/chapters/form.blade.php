<style>
    .chapter-form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 360px;
        gap: 16px;
        align-items: start;
    }
    .chapter-preview {
        position: sticky;
        top: 14px;
        border: 1px solid rgba(114, 84, 49, .28);
        border-radius: 12px;
        padding: 14px;
        background:
            radial-gradient(560px 200px at 8% -10%, rgba(255, 239, 208, .84), transparent 62%),
            linear-gradient(180deg, rgba(249, 238, 216, .98), rgba(236, 220, 189, .97));
        box-shadow: 0 8px 18px rgba(70, 45, 19, .12);
    }
    .chapter-preview h4 {
        margin: 0 0 10px;
        color: #6a4724;
        font-family: "Cinzel", "Times New Roman", serif;
        font-size: .98rem;
    }
    .chapter-preview-card {
        border-radius: 12px;
        border: 1px solid rgba(108,77,43,.22);
        background: rgba(255,255,255,.28);
        padding: 12px;
    }
    .chapter-preview-title {
        margin: 0 0 6px;
        color: #2f2013;
        font-family: "Cinzel", "Times New Roman", serif;
        font-size: 1.05rem;
    }
    .chapter-preview-summary {
        margin: 0;
        color: #6b5031;
        line-height: 1.45;
    }
    .chapter-preview-list {
        display: grid;
        gap: 8px;
        margin-top: 12px;
    }
    .chapter-preview-row {
        padding: 8px 10px;
        border-radius: 10px;
        border: 1px solid rgba(108,77,43,.14);
        background: rgba(255,255,255,.36);
        color: #5f4422;
        font-size: .88rem;
    }
    .chapter-preview-row.is-muted {
        color: #8b7760;
        font-style: italic;
    }
    .chapter-select-grid {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    }
    .chapter-characters-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 12px;
        align-items: start;
    }
    .chapter-characters-layout.has-panel {
        grid-template-columns: minmax(0, 1fr) minmax(360px, 420px);
    }
    .chapter-accordion {
        margin-top: 14px;
        border: 1px solid rgba(108,77,43,.18);
        border-radius: 12px;
        background: rgba(255,255,255,.22);
        overflow: hidden;
    }
    .chapter-accordion + .chapter-accordion {
        margin-top: 12px;
    }
    .chapter-accordion summary {
        list-style: none;
        cursor: pointer;
        padding: 12px 14px;
        color: #5d3f20;
        font-family: "Cinzel", "Times New Roman", serif;
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    .chapter-accordion summary::-webkit-details-marker {
        display: none;
    }
    .chapter-accordion summary::after {
        content: '›';
        font-size: 1.4rem;
        line-height: 1;
        transition: transform .18s ease;
        color: #8a6135;
    }
    .chapter-accordion[open] summary::after {
        transform: rotate(90deg);
    }
    .chapter-accordion-body {
        padding: 0 14px 14px;
    }
    .chapter-select-card {
        display: grid;
        grid-template-columns: 22px 1fr;
        gap: 2px 10px;
        border-radius: 10px;
        border: 1px solid rgba(108,77,43,.22);
        background: linear-gradient(180deg, rgba(255,255,255,.32), rgba(243,228,196,.58));
        padding: 12px 12px 11px;
        cursor: pointer;
    }
    .chapter-select-card input {
        margin: 2px 0 0;
        width: 18px;
        height: 18px;
        accent-color: #8a6135;
    }
    .chapter-select-card strong {
        display: block;
        color: #2f2013;
        font-family: "Cinzel", "Times New Roman", serif;
        font-size: 1rem;
        letter-spacing: .02em;
    }
    .chapter-select-card span {
        grid-column: 2;
        color: #6f5841;
        font-size: .88rem;
        line-height: 1.35;
    }
    .chapter-select-card small {
        grid-column: 2;
        color: #8b7760;
        font-size: .8rem;
        line-height: 1.35;
    }
    .chapter-select-card[data-has-details="1"] {
        border-color: rgba(127, 93, 50, .44);
        box-shadow: inset 0 0 0 1px rgba(127, 93, 50, .12);
    }
    .chapter-character-panel {
        position: sticky;
        top: 14px;
        justify-self: end;
        width: 320px;
        max-width: 320px;
        border: 1px solid rgba(108,77,43,.22);
        border-radius: 12px;
        background: linear-gradient(180deg, rgba(255,251,242,.98), rgba(244,228,194,.98));
        box-shadow: 0 12px 26px rgba(26, 16, 8, .12);
        padding: 14px;
        display: grid;
        gap: 12px;
    }
    .chapter-character-panel.is-hidden {
        display: none;
    }
    .chapter-character-panel h4 {
        margin: 0;
        color: #5d3f20;
        font-family: "Cinzel", "Times New Roman", serif;
        font-size: 1rem;
    }
    .chapter-character-panel .muted {
        margin: 0;
    }
    .chapter-character-panel .chapter-select-grid {
        grid-template-columns: 1fr;
    }
    .chapter-character-panel .chapter-select-card {
        padding: 10px 10px 9px;
    }
    .chapter-character-preview-image {
        width: 100%;
        border-radius: 10px;
        border: 1px solid rgba(108,77,43,.16);
        box-shadow: 0 6px 14px rgba(70, 45, 19, .08);
    }
    .chapter-modal-backdrop {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: stretch;
        justify-content: flex-end;
        background: transparent;
        z-index: 99999;
        padding: 16px;
        pointer-events: none;
    }
    .chapter-modal {
        width: min(420px, 100%);
        height: 100%;
        max-height: calc(100vh - 32px);
        border-radius: 16px 0 0 16px;
        border: 1px solid rgba(108,77,43,.22);
        background: linear-gradient(180deg, rgba(255,251,242,.98), rgba(244,228,194,.98));
        box-shadow: 0 24px 55px rgba(26, 16, 8, .34);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        pointer-events: auto;
    }
    .chapter-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px 10px;
        border-bottom: 1px solid rgba(108,77,43,.14);
    }
    .chapter-modal-header h4 {
        margin: 0;
        color: #5d3f20;
        font-family: "Cinzel", "Times New Roman", serif;
        font-size: 1rem;
    }
    .chapter-modal-header p {
        margin: 4px 0 0;
        color: #7a644b;
        font-size: .9rem;
        line-height: 1.4;
    }
    .chapter-modal-close {
        border: 0;
        background: transparent;
        color: #8a6135;
        font-size: 1.3rem;
        line-height: 1;
        cursor: pointer;
        padding: 0;
    }
    .chapter-modal-body {
        padding: 14px 16px 16px;
        display: grid;
        gap: 12px;
        overflow: auto;
    }
    .chapter-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }
    @media (max-width: 980px) {
        .chapter-form-grid { grid-template-columns: 1fr; }
        .chapter-preview { position: static; }
        .chapter-characters-layout,
        .chapter-characters-layout.has-panel { grid-template-columns: 1fr; }
        .chapter-character-panel { position: static; }
        .chapter-modal { width: min(100%, 460px); border-radius: 16px; }
    }
</style>

<section class="panel">
    @php
        $characterSourceMap = [];
        foreach ($characters as $character) {
            $characterSourceMap[(string) $character->id] = [
                'appearance_description' => (string) ($character->appearance_description ?? ''),
                'outfit_images' => collect($character->outfitImages ?? [])->map(function ($outfit) {
                    return [
                        'id' => (int) $outfit->id,
                        'mode' => (string) ($outfit->mode ?? ''),
                        'caption' => (string) ($outfit->caption ?? ''),
                        'image_path' => (string) ($outfit->image_path ?? ''),
                    ];
                })->values()->all(),
            ];
        }
    @endphp

    <form method="POST" action="{{ $action }}" class="chapter-form-grid">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif

        <div>
            <div class="field">
                <label for="chapter_world">{{ __('app.nav.worlds') }}</label>
                <input id="chapter_world" type="text" value="{{ $world->name }}" disabled>
            </div>

            <div class="field">
                <label for="chapter_date">{{ __('app.chapters.date') }}</label>
                <input id="chapter_date" type="date" name="chapter_date" value="{{ $dateValue }}" required>
            </div>

            <div class="field">
                <label for="chapter_title">{{ __('app.chapters.title') }}</label>
                <input id="chapter_title" type="text" name="title" value="{{ $titleValue }}" maxlength="180" required>
            </div>

            <div class="field">
                <label for="chapter_summary">{{ __('app.chapters.summary') }}</label>
                <textarea id="chapter_summary" name="summary" rows="4" maxlength="800">{{ $summaryValue }}</textarea>
            </div>

            <div class="field">
                <label for="chapter_description">{{ __('app.chapters.description') }}</label>
                <textarea id="chapter_description" name="description" rows="7">{{ $descriptionValue }}</textarea>
            </div>

                        <div class="chapter-characters-layout">
                <details class="chapter-accordion" open>
                    <summary>{{ __('app.chapters.linked_characters') }}</summary>
                    <div class="chapter-accordion-body">
                        @php
                            $emotionOptions = [
                                'Joie',
                                'Peur',
                                'Tristesse',
                                'Colère',
                                'Surprise',
                                'Calme',
                                'Tension',
                                'Fatigue',
                                'Détermination',
                                'Amour',
                            ];
                        @endphp
                        <div class="chapter-select-grid">
                            @foreach($characters as $character)
                                @php
                                    $characterDetail = $characterDetailsMap[(string) $character->id] ?? ['outfit_source' => '', 'outfit' => '', 'outfit_image_path' => '', 'emotion' => ''];
                                    $detailOutfit = trim((string) ($characterDetail['outfit'] ?? ''));
                                    $detailOutfitSource = trim((string) ($characterDetail['outfit_source'] ?? ''));
                                    $detailOutfitImage = trim((string) ($characterDetail['outfit_image_path'] ?? ''));
                                    $detailEmotion = trim((string) ($characterDetail['emotion'] ?? ''));
                                    $detailSummaryParts = array_values(array_filter([$detailOutfit, $detailEmotion], function ($value) {
                                        return $value !== '';
                                    }));
                                    $detailSummary = !empty($detailSummaryParts)
                                        ? implode(' · ', $detailSummaryParts)
                                        : __('app.chapters.character_details_hint');
                                @endphp
                                <label class="chapter-select-card" data-character-card="{{ $character->id }}" data-has-details="{{ ($detailOutfit !== '' || $detailOutfitSource !== '' || $detailOutfitImage !== '') && $detailEmotion !== '' ? '1' : '0' }}">
                                    <input type="checkbox" name="character_ids[]" value="{{ $character->id }}" data-character-checkbox="{{ $character->id }}" {{ in_array((int) $character->id, $selectedCharacterIds, true) ? 'checked' : '' }}>
                                    <strong>{{ $character->display_name }}</strong>
                                    <span>{{ $character->role ?: ($character->preferred_color ?: __('app.chapters.no_characters')) }}</span>
                                    <small data-character-summary="{{ $character->id }}">{{ $detailSummary }}</small>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </details>

                <aside class="chapter-character-panel is-hidden" id="character_detail_panel" aria-hidden="true">
                    <h4 id="character_detail_title">{{ __('app.chapters.character_details_title') }}</h4>
                    <p class="muted" id="character_detail_context">{{ __('app.chapters.character_details_help') }}</p>
                    <div class="field" style="margin:0;">
                        <label>{{ __('app.chapters.character_outfit_source') }}</label>
                        <div class="stack" style="gap:8px;">
                            <label class="chapter-select-card" style="grid-template-columns:22px 1fr;">
                                <input type="radio" id="character_detail_source_photo" name="character_detail_source_ui" value="photo" checked>
                                <strong>{{ __('app.chapters.character_outfit_photos') }}</strong>
                                <span>{{ __('app.chapters.character_outfit_photos_help') }}</span>
                            </label>
                            <label class="chapter-select-card" style="grid-template-columns:22px 1fr;">
                                <input type="radio" id="character_detail_source_description" name="character_detail_source_ui" value="description">
                                <strong>{{ __('app.chapters.character_outfit_description') }}</strong>
                                <span>{{ __('app.chapters.character_outfit_description_help') }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="field" style="margin:0;" id="character_detail_photo_field">
                        <label>{{ __('app.chapters.character_outfit_photos') }}</label>
                        <div class="chapter-select-grid" id="character_detail_photo_list"></div>
                    </div>
                    <div class="field" style="margin:0; display:none;" id="character_detail_description_field">
                        <label for="character_detail_description">{{ __('app.chapters.character_outfit_description') }}</label>
                        <textarea id="character_detail_description" rows="6" placeholder="{{ __('app.chapters.character_outfit_placeholder') }}"></textarea>
                    </div>
                    <div class="field" style="margin:0;">
                        <label for="character_detail_outfit">{{ __('app.chapters.character_outfit') }}</label>
                        <input id="character_detail_outfit" type="text" maxlength="255" readonly>
                    </div>
                    <div class="field" style="margin:0;">
                        <label for="character_detail_emotion">{{ __('app.chapters.character_emotion') }}</label>
                        <select id="character_detail_emotion">
                            <option value="">{{ __('app.chapters.character_emotion_placeholder') }}</option>
                            @foreach($emotionOptions as $emotionOption)
                                <option value="{{ $emotionOption }}">{{ $emotionOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="chapter-modal-actions">
                        <button class="btn secondary" type="button" id="character_detail_cancel">{{ __('app.chapters.character_details_cancel') }}</button>
                        <button class="btn" type="button" id="character_detail_save">{{ __('app.chapters.character_details_save') }}</button>
                    </div>
                </aside>
            </div>

            <details class="chapter-accordion" open>
                <summary>{{ __('app.chapters.linked_places') }}</summary>
                <div class="chapter-accordion-body">
                    <div class="chapter-select-grid">
                        @foreach($places as $place)
                            <label class="chapter-select-card">
                                <input type="checkbox" name="place_ids[]" value="{{ $place->id }}" {{ in_array((int) $place->id, $selectedPlaceIds, true) ? 'checked' : '' }}>
                                <strong>{{ $place->name }}</strong>
                                <span>{{ $place->region ?: __('app.chapters.no_places') }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </details>

            <div class="stack">
                <button class="btn" type="submit">{{ $submitLabel }}</button>
                <a class="btn secondary" href="{{ route('manage.chapters.index') }}">{{ __('app.chapters.back') }}</a>
            </div>
        </div>

        <aside class="chapter-preview">
            <h4>Aperçu</h4>
            <div class="chapter-preview-card">
                <h5 class="chapter-preview-title" id="preview_title">{{ $titleValue ?: __('app.chapters.new_title') }}</h5>
                <p class="chapter-preview-summary" id="preview_summary" style="{{ !empty($summaryValue) ? '' : 'display:none;' }}">{{ $summaryValue ?? '' }}</p>
                <div class="chapter-preview-row" id="preview_date">
                    {{ $dateValue ? \Illuminate\Support\Carbon::parse($dateValue)->format('d/m/Y') : __('app.chapters.no_date') }}
                </div>
                <div class="chapter-preview-list">
                    <div class="chapter-preview-row" id="preview_characters">{{ count($selectedCharacterIds) }} {{ __('app.chapters.linked_characters') }}</div>
                    <div class="chapter-preview-row" id="preview_places">{{ count($selectedPlaceIds) }} {{ __('app.chapters.linked_places') }}</div>
                </div>
            </div>
        </aside>

        <div id="character_details_store" style="display:none;">
            @foreach($characterDetailsMap as $characterId => $characterDetail)
                <input type="hidden" name="character_details[{{ $characterId }}][outfit_source]" value="{{ $characterDetail['outfit_source'] ?? '' }}" data-detail-field="{{ $characterId }}" data-detail-key="outfit_source">
                <input type="hidden" name="character_details[{{ $characterId }}][outfit]" value="{{ $characterDetail['outfit'] ?? '' }}" data-detail-field="{{ $characterId }}" data-detail-key="outfit">
                <input type="hidden" name="character_details[{{ $characterId }}][outfit_image_path]" value="{{ $characterDetail['outfit_image_path'] ?? '' }}" data-detail-field="{{ $characterId }}" data-detail-key="outfit_image_path">
                <input type="hidden" name="character_details[{{ $characterId }}][emotion]" value="{{ $characterDetail['emotion'] ?? '' }}" data-detail-field="{{ $characterId }}" data-detail-key="emotion">
            @endforeach
        </div>
    </form>

    
</section>

<script>
    (function () {
        const titleInput = document.getElementById('chapter_title');
        const summaryInput = document.getElementById('chapter_summary');
        const descriptionInput = document.getElementById('chapter_description');
        const dateInput = document.getElementById('chapter_date');
        const titleOutput = document.getElementById('preview_title');
        const summaryOutput = document.getElementById('preview_summary');
        const characterOutput = document.getElementById('preview_characters');
        const placeOutput = document.getElementById('preview_places');
        const chapterForm = document.querySelector('.chapter-form-grid');
        const characterLayout = document.querySelector('.chapter-characters-layout');
        const detailStore = document.getElementById('character_details_store');
        const detailPanel = document.getElementById('character_detail_panel');
        const detailCancel = document.getElementById('character_detail_cancel');
        const detailSave = document.getElementById('character_detail_save');
        const detailOutfit = document.getElementById('character_detail_outfit');
        const detailEmotion = document.getElementById('character_detail_emotion');
        const detailPhotoField = document.getElementById('character_detail_photo_field');
        const detailPhotoList = document.getElementById('character_detail_photo_list');
        const detailDescriptionField = document.getElementById('character_detail_description_field');
        const detailDescription = document.getElementById('character_detail_description');
        const detailSourcePhoto = document.getElementById('character_detail_source_photo');
        const detailSourceDescription = document.getElementById('character_detail_source_description');
        const detailTitle = document.getElementById('character_detail_title');
        const detailContext = document.getElementById('character_detail_context');
        const detailHint = @json(__('app.chapters.character_details_hint'));
        const detailRequired = @json(__('app.chapters.character_details_required'));
        const detailTitleBase = @json(__('app.chapters.character_details_title'));
        const characterSourceMap = @json($characterSourceMap);
        const mediaBaseUrl = @json(url('/media/')) + '/';
        const characterChecks = Array.from(document.querySelectorAll('input[name="character_ids[]"]'));
        const placeChecks = Array.from(document.querySelectorAll('input[name="place_ids[]"]'));
        let activeCharacterId = null;
        let activeCheckbox = null;


        if (!titleInput || !summaryInput || !descriptionInput || !dateInput || !titleOutput || !characterOutput || !placeOutput) {
            return;
        }

        function detailFieldSelector(characterId, key) {
            return '[data-detail-field="' + characterId + '"][data-detail-key="' + key + '"]';
        }

        function getDetailField(characterId, key) {
            return document.querySelector(detailFieldSelector(characterId, key));
        }

        function ensureDetailField(characterId, key) {
            if (!detailStore) {
                return null;
            }

            var field = getDetailField(characterId, key);
            if (field) {
                return field;
            }

            field = document.createElement('input');
            field.type = 'hidden';
            field.name = 'character_details[' + characterId + '][' + key + ']';
            field.setAttribute('data-detail-field', characterId);
            field.setAttribute('data-detail-key', key);
            detailStore.appendChild(field);

            return field;
        }

        function readDetails(characterId) {
            var sourceField = getDetailField(characterId, 'outfit_source');
            var outfitField = getDetailField(characterId, 'outfit');
            var imageField = getDetailField(characterId, 'outfit_image_path');
            var emotionField = getDetailField(characterId, 'emotion');

            return {
                outfit_source: sourceField ? sourceField.value : '',
                outfit: outfitField ? outfitField.value : '',
                outfit_image_path: imageField ? imageField.value : '',
                emotion: emotionField ? emotionField.value : '',
            };
        }

        function updateCharacterSummary(characterId) {
            var summary = document.querySelector('[data-character-summary="' + characterId + '"]');
            var card = document.querySelector('[data-character-card="' + characterId + '"]');
            var details = readDetails(characterId);
            var filled = details.emotion.trim() !== '';
            var sourceLabel = details.outfit_source === 'description' ? @json(__('app.chapters.character_outfit_description')) : @json(__('app.chapters.character_outfit_photos'));
            var outfitLabel = details.outfit.trim();
            if (!outfitLabel && details.outfit_image_path.trim() !== '') {
                outfitLabel = @json(__('app.chapters.character_outfit_photos'));
            }

            if (card) {
                card.setAttribute('data-has-details', filled ? '1' : '0');
            }

            if (summary) {
                summary.textContent = filled
                    ? ((outfitLabel !== '' || details.outfit_image_path.trim() !== '')
                        ? (sourceLabel + ' · ' + (outfitLabel || detailHint) + ' · ' + details.emotion.trim())
                        : details.emotion.trim())
                    : detailHint;
            }
        }

        function syncAllCharacterSummaries() {
            characterChecks.forEach(function (input) {
                updateCharacterSummary(String(input.value));
            });
        }

        function refreshOutfitSourceUI() {
            var useDescription = detailSourceDescription && detailSourceDescription.checked;
            if (detailPhotoField) {
                detailPhotoField.style.display = useDescription ? 'none' : '';
            }
            if (detailDescriptionField) {
                detailDescriptionField.style.display = useDescription ? '' : 'none';
            }
        }

        function syncActiveCharacterDetails() {
            if (!activeCharacterId) {
                return;
            }

            var selectedSource = detailSourceDescription && detailSourceDescription.checked ? 'description' : 'photo';
            var outfitImagePath = '';
            var outfit = '';

            if (selectedSource === 'photo') {
                var selectedPhoto = detailPhotoList ? detailPhotoList.querySelector('input[name="character_detail_photo_choice"]:checked') : null;
                if (selectedPhoto && selectedPhoto.value) {
                    outfitImagePath = selectedPhoto.value;
                    var selectedLabel = selectedPhoto.closest('label');
                    var selectedSpan = selectedLabel ? selectedLabel.querySelector('span') : null;
                    outfit = selectedSpan ? (selectedSpan.textContent || '') : '';
                }
            } else if (detailDescription) {
                outfit = detailDescription.value.trim();
            }

            var emotion = detailEmotion ? detailEmotion.value.trim() : '';

            var sourceField = ensureDetailField(activeCharacterId, 'outfit_source');
            var outfitField = ensureDetailField(activeCharacterId, 'outfit');
            var imageField = ensureDetailField(activeCharacterId, 'outfit_image_path');
            var emotionField = ensureDetailField(activeCharacterId, 'emotion');
            if (sourceField) {
                sourceField.value = selectedSource;
            }
            if (outfitField) {
                outfitField.value = outfit;
            }
            if (imageField) {
                imageField.value = outfitImagePath;
            }
            if (emotionField) {
                emotionField.value = emotion;
            }

            updateCharacterSummary(activeCharacterId);
        }

        function showModal() {
            if (!detailPanel) {
                return;
            }

            if (characterLayout) {
                characterLayout.classList.add('has-panel');
            }
            detailPanel.classList.remove('is-hidden');
            detailPanel.setAttribute('aria-hidden', 'false');
        }

        function hideModal(uncheckCurrent) {
            if (!detailPanel) {
                return;
            }

            if (uncheckCurrent && activeCheckbox) {
                activeCheckbox.checked = false;
            }

            if (characterLayout) {
                characterLayout.classList.remove('has-panel');
            }
            detailPanel.classList.add('is-hidden');
            detailPanel.setAttribute('aria-hidden', 'true');
            activeCharacterId = null;
            activeCheckbox = null;
        }

        function openCharacterModal(checkbox) {
            if (!detailPanel || !detailOutfit || !detailEmotion) {
                return;
            }

            activeCheckbox = checkbox;
            activeCharacterId = String(checkbox.value);
            var sourceData = characterSourceMap[activeCharacterId] || { appearance_description: '', outfit_images: [] };
            var outfitImages = sourceData.outfit_images || [];
            var appearanceDescription = (sourceData.appearance_description || '').trim();

            var card = checkbox.closest('[data-character-card]');
            var characterName = card ? card.querySelector('strong') : null;
            if (detailTitle) {
                detailTitle.textContent = detailTitleBase + (characterName && characterName.textContent ? ' — ' + characterName.textContent.trim() : '');
            }

            if (detailContext) {
                detailContext.textContent = @json(__('app.chapters.character_details_help'));
            }

            var details = readDetails(activeCharacterId);
            var selectedSource = details.outfit_source || (outfitImages.length > 0 ? 'photo' : 'description');
            if (detailSourcePhoto) {
                detailSourcePhoto.checked = selectedSource === 'photo';
            }
            if (detailSourceDescription) {
                detailSourceDescription.checked = selectedSource === 'description';
            }
            if (detailDescription) {
                detailDescription.value = appearanceDescription;
            }

            if (detailPhotoList) {
                detailPhotoList.innerHTML = '';
                outfitImages.forEach(function (photo) {
                    var wrapper = document.createElement('label');
                    wrapper.className = 'chapter-select-card';
                    wrapper.style.gridTemplateColumns = '22px 1fr';

                    var radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = 'character_detail_photo_choice';
                    radio.value = photo.image_path || '';

                    if (details.outfit_image_path && details.outfit_image_path === photo.image_path) {
                        radio.checked = true;
                    }

                    var strong = document.createElement('strong');
                    strong.textContent = photo.mode || @json(__('app.chapters.character_outfit_photos'));

                    var span = document.createElement('span');
                    span.textContent = photo.caption || photo.image_path;

                    if (photo.image_path) {
                        var img = document.createElement('img');
                        img.src = mediaBaseUrl + encodeURIComponent(photo.image_path);
                        img.alt = photo.caption || photo.mode || 'tenue';
                        img.style.width = '100%';
                        img.style.borderRadius = '8px';
                        img.style.marginTop = '8px';
                        wrapper.appendChild(radio);
                        var content = document.createElement('div');
                        content.appendChild(strong);
                        content.appendChild(span);
                        content.appendChild(img);
                        wrapper.appendChild(content);
                    } else {
                        wrapper.appendChild(radio);
                        wrapper.appendChild(strong);
                        wrapper.appendChild(span);
                    }

                    detailPhotoList.appendChild(wrapper);
                });
            }

            detailOutfit.value = details.outfit || '';
            detailEmotion.value = details.emotion || '';

            showModal();
            refreshOutfitSourceUI();

            window.setTimeout(function () {
                if (selectedSource === 'description' && detailDescription) {
                    detailDescription.focus();
                } else if (detailPhotoList) {
                    var firstRadio = detailPhotoList.querySelector('input[type="radio"]');
                    if (firstRadio) {
                        firstRadio.focus();
                    }
                }
            }, 0);

            updatePreview();
        }

        function saveCharacterDetails() {
            if (!activeCharacterId || !detailOutfit || !detailEmotion) {
                return;
            }

            var emotion = detailEmotion.value.trim();

            if (emotion === '') {
                window.alert(detailRequired);
                return;
            }
            syncActiveCharacterDetails();
            updateCharacterSummary(activeCharacterId);
            hideModal(false);
            updatePreview();
        }

        const updatePreview = () => {
            titleOutput.textContent = titleInput.value.trim() || @json(__('app.chapters.new_title'));
            if (summaryOutput) {
                summaryOutput.textContent = summaryInput.value.trim() || '';
                summaryOutput.style.display = summaryInput.value.trim() ? '' : 'none';
            }
            const dateValue = dateInput.value ? new Date(`${dateInput.value}T00:00:00`) : null;
            const dateLabel = dateValue
                ? new Intl.DateTimeFormat(document.documentElement.lang || 'fr-FR').format(dateValue)
                : @json(__('app.chapters.no_date'));
            const dateRow = document.getElementById('preview_date');
            if (dateRow) {
                dateRow.textContent = dateLabel;
            }
            characterOutput.textContent = `${characterChecks.filter((input) => input.checked).length} @json(__('app.chapters.linked_characters'))`;
            placeOutput.textContent = `${placeChecks.filter((input) => input.checked).length} @json(__('app.chapters.linked_places'))`;
        };

        titleInput.addEventListener('input', updatePreview);
        summaryInput.addEventListener('input', updatePreview);
        descriptionInput.addEventListener('input', updatePreview);
        dateInput.addEventListener('input', updatePreview);

        characterChecks.forEach(function (input) {
            input.addEventListener('change', function () {
                var characterId = String(input.value);
                if (input.checked) {
                    openCharacterModal(input);
                } else if (activeCharacterId === characterId) {
                    var fallback = characterChecks.find(function (checkbox) {
                        return checkbox.checked;
                    });
                    if (fallback) {
                        openCharacterModal(fallback);
                    } else {
                        hideModal(false);
                        updatePreview();
                    }
                } else {
                    updatePreview();
                }
            });
        });
        placeChecks.forEach((input) => input.addEventListener('change', updatePreview));
        if (detailPanel) {
            detailPanel.classList.add('is-hidden');
            detailPanel.setAttribute('aria-hidden', 'true');
        }
        if (characterLayout) {
            characterLayout.classList.remove('has-panel');
        }
        if (detailSave) {
            detailSave.addEventListener('click', saveCharacterDetails);
        }
        if (detailSourcePhoto) {
            detailSourcePhoto.addEventListener('change', function () {
                refreshOutfitSourceUI();
                syncActiveCharacterDetails();
            });
        }
        if (detailSourceDescription) {
            detailSourceDescription.addEventListener('change', function () {
                refreshOutfitSourceUI();
                syncActiveCharacterDetails();
            });
        }
        if (detailEmotion) {
            detailEmotion.addEventListener('change', syncActiveCharacterDetails);
        }
        if (detailDescription) {
            detailDescription.addEventListener('input', syncActiveCharacterDetails);
        }
        if (detailPhotoList) {
            detailPhotoList.addEventListener('change', syncActiveCharacterDetails);
        }
        if (chapterForm) {
            chapterForm.addEventListener('submit', function () {
                syncActiveCharacterDetails();
            });
        }
        if (detailCancel) {
            detailCancel.addEventListener('click', function () {
                hideModal(true);
                updatePreview();
            });
        }
        syncAllCharacterSummaries();
        updatePreview();
    })();
</script>
