@extends('manage.layout')

@section('title', 'Gestion - Editer relation')
@section('header', 'Editer relation')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.relations.update', $relation) }}">
            @csrf @method('PUT')
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Personnage source</label>
                    <select name="from_character_id" required>
                        @foreach($characters as $character)
                            <option value="{{ $character->id }}" data-gender="{{ $character->gender }}" {{ old('from_character_id', $relation->from_character_id) == $character->id ? 'selected' : '' }}>{{ $character->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" style="grid-column: span 2;">
                    <label>Personnage cible</label>
                    <select name="to_character_id" required>
                        @foreach($characters as $character)
                            <option value="{{ $character->id }}" {{ old('to_character_id', $relation->to_character_id) == $character->id ? 'selected' : '' }}>{{ $character->display_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Type de relation</label>
                    @php
                        $currentType = old('relation_type', $relation->relation_type);
                        $baseTypes = ['pere', 'mere', 'frere', 'soeur', 'jumeau', 'jumelle', 'demi-frere', 'demi-soeur', 'amour', 'ami', 'allie', 'ennemi', 'mentor', 'rival'];
                        $isChildType = in_array($currentType, ['fils', 'fille', 'fils/fille'], true);
                    @endphp
                    <select name="relation_type" required>
                        <option value="">Selectionner</option>
                        @foreach($baseTypes as $type)
                            <option value="{{ $type }}" {{ $currentType === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                        <option data-child-role="1" value="{{ $currentType === 'fille' ? 'fille' : 'fils' }}" {{ $isChildType ? 'selected' : '' }}>
                            {{ $currentType === 'fille' ? 'Fille' : 'Fils' }}
                        </option>
                        @if(!$isChildType && !in_array($currentType, $baseTypes, true) && $currentType !== '')
                            <option value="{{ $currentType }}" selected>{{ ucfirst($currentType) }}</option>
                        @endif
                    </select>
                </div>
                <div class="field">
                    <label>Intensite (1-10)</label>
                    <input type="number" min="1" max="10" name="intensity" value="{{ old('intensity', $relation->intensity) }}">
                </div>
                <div class="field">
                    <label>Sens</label>
                    <select name="is_bidirectional">
                        @php $bi = (string) old('is_bidirectional', $relation->is_bidirectional ? '1' : '0'); @endphp
                        <option value="1" {{ $bi === '1' ? 'selected' : '' }}>Bidirectionnelle</option>
                        <option value="0" {{ $bi === '0' ? 'selected' : '' }}>Unidirectionnelle</option>
                    </select>
                </div>
            </div>
            <div class="field">
                <label>Description</label>
                <textarea name="description">{{ old('description', $relation->description) }}</textarea>
            </div>
            <div class="stack">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('manage.relations.index') }}">Retour</a>
            </div>
        </form>
    </section>
    <script>
        (function () {
            const fromSelect = document.querySelector('select[name="from_character_id"]');
            const typeSelect = document.querySelector('select[name="relation_type"]');
            if (!fromSelect || !typeSelect) return;

            const childOption = typeSelect.querySelector('option[data-child-role="1"]');
            if (!childOption) return;

            function refreshChildRole() {
                const selected = fromSelect.selectedOptions[0];
                const gender = ((selected && selected.dataset.gender) || '').toLowerCase();
                let value = 'fils/fille';
                if (gender === 'homme') value = 'fils';
                if (gender === 'femme') value = 'fille';

                childOption.value = value;
                childOption.textContent = value.charAt(0).toUpperCase() + value.slice(1);

                if (typeSelect.value === 'fils' || typeSelect.value === 'fille' || typeSelect.value === 'fils/fille') {
                    typeSelect.value = value;
                }
            }

            fromSelect.addEventListener('change', refreshChildRole);
            refreshChildRole();
        })();
    </script>
@endsection
