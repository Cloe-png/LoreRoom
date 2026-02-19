@extends('manage.layout')

@section('title', 'Gestion - Nouvelle relation')
@section('header', 'Nouvelle relation')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.relations.store') }}">
            @csrf
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Personnage source</label>
                    <select name="from_character_id" required>
                        <option value="">Sélectionner</option>
                        @foreach($characters as $character)
                            <option value="{{ $character->id }}" data-gender="{{ $character->gender }}" {{ old('from_character_id') == $character->id ? 'selected' : '' }}>{{ $character->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field" style="grid-column: span 2;">
                    <label>Personnage cible</label>
                    <select name="to_character_id" required>
                        <option value="">Sélectionner</option>
                        @foreach($characters as $character)
                            <option value="{{ $character->id }}" {{ old('to_character_id') == $character->id ? 'selected' : '' }}>{{ $character->display_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Type de relation</label>
                    @php
                        $baseTypes = ['pere', 'mere', 'frere', 'soeur', 'jumeau', 'jumelle', 'demi-frere', 'demi-soeur', 'amour', 'ami', 'allie', 'ennemi', 'mentor', 'rival'];
                    @endphp
                    <select name="relation_type" required>
                        <option value="">Sélectionner</option>
                        @foreach($baseTypes as $type)
                            <option value="{{ $type }}" {{ old('relation_type') === $type ? 'selected' : '' }}>{{ str_replace(['pere','mere','frere','soeur','demi-frere','demi-soeur','allie'], ['père','mère','frère','sœur','demi-frère','demi-sœur','allié'], ucfirst($type)) }}</option>
                        @endforeach
                        <option data-child-role="1" value="{{ old('relation_type') === 'fille' ? 'fille' : 'fils' }}" {{ in_array(old('relation_type'), ['fils', 'fille', 'fils/fille'], true) ? 'selected' : '' }}>
                            {{ old('relation_type') === 'fille' ? 'Fille' : 'Fils' }}
                        </option>
                    </select>
                </div>
                <div class="field">
                    <label>Sens</label>
                    <select name="is_bidirectional">
                        <option value="1" {{ old('is_bidirectional', '1') == '1' ? 'selected' : '' }}>Bidirectionnelle</option>
                        <option value="0" {{ old('is_bidirectional') === '0' ? 'selected' : '' }}>Unidirectionnelle</option>
                    </select>
                </div>
            </div>
            <div class="field">
                <label>Description</label>
                <textarea name="description">{{ old('description') }}</textarea>
            </div>
            <div class="stack">
                <button class="btn" type="submit">Créer</button>
                <a class="btn secondary" href="{{ route('manage.relations.index') }}">Annuler</a>
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

