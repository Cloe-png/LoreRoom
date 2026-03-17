@extends('manage.layout')

@section('title', 'Gestion - Éditer faction')
@section('header', 'Éditer faction')

@section('content')
    @php
        $memberRows = old('members', $memberRows ?? []);
        if (empty($memberRows)) {
            $memberRows = [['character_id' => '', 'role' => '']];
        }
        $relationRows = old('relations', $relationRows ?? []);
        if (empty($relationRows)) {
            $relationRows = [[
                'related_faction_id' => '',
                'relation_type' => 'allié',
                'description' => '',
                'is_bidirectional' => true,
            ]];
        }
        $diplomaRows = old('diplomas', $diplomaRows ?? []);
        if (empty($diplomaRows)) {
            $diplomaRows = [['name' => '', 'level' => '', 'description' => '']];
        }
    @endphp

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
        .accordion-body { padding: 10px 12px 2px; }
        .small-muted { font-size: .82rem; color: #6f5841; }
        .row-card { margin-top:10px; padding:10px; }
    </style>

    <section class="panel">
        <form method="POST" action="{{ route('manage.factions.update', $faction) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>

            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Nom de la faction</label>
                    <input type="text" name="name" value="{{ old('name', $faction->name) }}" required>
                </div>
                <div class="field" style="grid-column: span 2;">
                    <label>Type (ex: armée, guilde...)</label>
                    <select name="type">
                        <option value="">-</option>
                        @foreach($typeOptions as $type)
                            <option value="{{ $type }}" {{ old('type', $faction->type) === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Description</label>
                <textarea name="summary">{{ old('summary', $faction->summary) }}</textarea>
            </div>

            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Devise / slogan</label>
                    <input type="text" name="motto" value="{{ old('motto', $faction->motto) }}">
                </div>
                <div class="field">
                    <label>Date de création</label>
                    <input type="date" name="founded_at" value="{{ old('founded_at', optional($faction->founded_at)->format('Y-m-d')) }}">
                </div>
                <div class="field">
                    <label>Statut</label>
                    <select name="status">
                        <option value="">-</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status }}" {{ old('status', $faction->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Logo</label>
                <input type="file" name="logo" accept="image/*">
                @if($faction->logo_path)
                    <p class="muted">Logo actuel: {{ $faction->logo_path }}</p>
                @endif
            </div>

            <div class="grid-4">
                <div class="field">
                    <label>Leader</label>
                    <select name="leader_id">
                        <option value="">-</option>
                        @foreach($characters as $character)
                            <option value="{{ $character->id }}" {{ (string)old('leader_id', $faction->leader_id) === (string)$character->id ? 'selected' : '' }}>
                                {{ $character->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Co-leader / bras droit</label>
                    <select name="co_leader_id">
                        <option value="">-</option>
                        @foreach($characters as $character)
                            <option value="{{ $character->id }}" {{ (string)old('co_leader_id', $faction->co_leader_id) === (string)$character->id ? 'selected' : '' }}>
                                {{ $character->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Fondateur</label>
                    <select name="founder_id">
                        <option value="">-</option>
                        @foreach($characters as $character)
                            <option value="{{ $character->id }}" {{ (string)old('founder_id', $faction->founder_id) === (string)$character->id ? 'selected' : '' }}>
                                {{ $character->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <details class="accordion" open>
                <summary>Membres</summary>
                <div class="accordion-body">
                    <div id="members-list">
                        @foreach($memberRows as $i => $row)
                            <div class="panel row-card" data-member-row>
                                <div class="grid-4">
                                    <div class="field" style="grid-column: span 2;">
                                        <label>Personnage</label>
                                        <select name="members[{{ $i }}][character_id]">
                                            <option value="">Aucun</option>
                                            @foreach($characters as $character)
                                                <option value="{{ $character->id }}" {{ (string)($row['character_id'] ?? '') === (string)$character->id ? 'selected' : '' }}>
                                                    {{ $character->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                <div class="field" style="grid-column: span 2;">
                                    <label>Rôle dans la faction</label>
                                    <input type="text" name="members[{{ $i }}][role]" value="{{ $row['role'] ?? '' }}" placeholder="Chef, soldat, espion...">
                                </div>
                                <div class="field">
                                    <label>Grade</label>
                                    <input type="text" name="members[{{ $i }}][grade]" value="{{ $row['grade'] ?? '' }}">
                                </div>
                                <div class="field">
                                    <label>Date d'entrée</label>
                                    <input type="date" name="members[{{ $i }}][joined_at]" value="{{ $row['joined_at'] ?? '' }}">
                                </div>
                                <div class="field">
                                    <label>Statut</label>
                                    <select name="members[{{ $i }}][status]">
                                        <option value="">-</option>
                                        @foreach($memberStatusOptions as $status)
                                            <option value="{{ $status }}" {{ (string)($row['status'] ?? '') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button class="btn danger" type="button" data-remove-member>Retirer</button>
                        </div>
                    @endforeach
                    </div>
                    <div class="stack" style="margin-bottom:10px;">
                        <button class="btn secondary" type="button" id="add-member-btn">Ajouter un membre</button>
                        <span class="small-muted">Tu peux laisser une ligne vide si besoin.</span>
                    </div>
                </div>
            </details>

            <details class="accordion" open>
                <summary>Relations entre factions</summary>
                <div class="accordion-body">
                    <div id="relations-list">
                        @foreach($relationRows as $i => $row)
                            <div class="panel row-card" data-relation-row>
                                <div class="grid-4">
                                    <div class="field" style="grid-column: span 2;">
                                        <label>Faction liée</label>
                                        <select name="relations[{{ $i }}][related_faction_id]">
                                            <option value="">Aucune</option>
                                            @foreach($otherFactions as $other)
                                                <option value="{{ $other->id }}" {{ (string)($row['related_faction_id'] ?? '') === (string)$other->id ? 'selected' : '' }}>
                                                    {{ $other->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label>Type</label>
                                        <select name="relations[{{ $i }}][relation_type]">
                                            @foreach(['allié','ennemi','neutre'] as $type)
                                                <option value="{{ $type }}" {{ (string)($row['relation_type'] ?? '') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field">
                                        <label>Bidirectionnelle</label>
                                        <select name="relations[{{ $i }}][is_bidirectional]">
                                            <option value="1" {{ !empty($row['is_bidirectional']) ? 'selected' : '' }}>Oui</option>
                                            <option value="0" {{ empty($row['is_bidirectional']) ? 'selected' : '' }}>Non</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="field">
                                    <label>Description</label>
                                    <textarea name="relations[{{ $i }}][description]">{{ $row['description'] ?? '' }}</textarea>
                                </div>
                                <button class="btn danger" type="button" data-remove-relation>Retirer</button>
                            </div>
                        @endforeach
                    </div>
                    <div class="stack" style="margin-bottom:10px;">
                        <button class="btn secondary" type="button" id="add-relation-btn">Ajouter une relation</button>
                    </div>
                </div>
            </details>

            <details class="accordion" open>
                <summary>Diplômes (écoles / universités)</summary>
                <div class="accordion-body">
                    <div id="diplomas-list">
                        @foreach($diplomaRows as $i => $row)
                            <div class="panel row-card" data-diploma-row>
                                <div class="grid-4">
                                    <div class="field" style="grid-column: span 2;">
                                        <label>Nom du diplôme</label>
                                        <input type="text" name="diplomas[{{ $i }}][name]" value="{{ $row['name'] ?? '' }}">
                                    </div>
                                    <div class="field" style="grid-column: span 2;">
                                        <label>Niveau</label>
                                        <input type="text" name="diplomas[{{ $i }}][level]" value="{{ $row['level'] ?? '' }}" placeholder="Licence, Master, Doctorat...">
                                    </div>
                                </div>
                                <div class="field">
                                    <label>Description</label>
                                    <textarea name="diplomas[{{ $i }}][description]">{{ $row['description'] ?? '' }}</textarea>
                                </div>
                                <button class="btn danger" type="button" data-remove-diploma>Retirer</button>
                            </div>
                        @endforeach
                    </div>
                    <div class="stack" style="margin-bottom:10px;">
                        <button class="btn secondary" type="button" id="add-diploma-btn">Ajouter un diplôme</button>
                    </div>
                </div>
            </details>

            <div class="stack">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('manage.factions.index') }}">Retour</a>
            </div>
        </form>
    </section>

    <template id="member-row-template">
        <div class="panel row-card" data-member-row>
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Personnage</label>
                    <select data-field="character_id">
                        <option value="">Aucun</option>
                        @foreach($characters as $character)
                            <option value="{{ $character->id }}">{{ $character->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" style="grid-column: span 2;">
                    <label>Rôle dans la faction</label>
                    <input type="text" data-field="role" placeholder="Chef, soldat, espion...">
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
                        @foreach($memberStatusOptions as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button class="btn danger" type="button" data-remove-member>Retirer</button>
        </div>
    </template>

    <template id="relation-row-template">
        <div class="panel row-card" data-relation-row>
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Faction liée</label>
                    <select data-field="related_faction_id">
                        <option value="">Aucune</option>
                        @foreach($otherFactions as $other)
                            <option value="{{ $other->id }}">{{ $other->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Type</label>
                    <select data-field="relation_type">
                        @foreach(['allié','ennemi','neutre'] as $type)
                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Bidirectionnelle</label>
                    <select data-field="is_bidirectional">
                        <option value="1">Oui</option>
                        <option value="0">Non</option>
                    </select>
                </div>
            </div>
            <div class="field">
                <label>Description</label>
                <textarea data-field="description"></textarea>
            </div>
            <button class="btn danger" type="button" data-remove-relation>Retirer</button>
        </div>
    </template>

    <template id="diploma-row-template">
        <div class="panel row-card" data-diploma-row>
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Nom du diplôme</label>
                    <input type="text" data-field="name">
                </div>
                <div class="field" style="grid-column: span 2;">
                    <label>Niveau</label>
                    <input type="text" data-field="level" placeholder="Licence, Master, Doctorat...">
                </div>
            </div>
            <div class="field">
                <label>Description</label>
                <textarea data-field="description"></textarea>
            </div>
            <button class="btn danger" type="button" data-remove-diploma>Retirer</button>
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

            bindCollection('members-list', 'add-member-btn', 'member-row-template', 'data-member-row', 'data-remove-member', 'members');
            bindCollection('relations-list', 'add-relation-btn', 'relation-row-template', 'data-relation-row', 'data-remove-relation', 'relations');
            bindCollection('diplomas-list', 'add-diploma-btn', 'diploma-row-template', 'data-diploma-row', 'data-remove-diploma', 'diplomas');
        })();
    </script>
@endsection
