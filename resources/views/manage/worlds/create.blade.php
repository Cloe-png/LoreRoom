@extends('manage.layout')

@section('title', 'Gestion - Nouveau monde')
@section('header', 'Nouveau monde')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.worlds.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="field">
                <label>Nom</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="field">
                <label>Type</label>
                <select name="geography_type" required>
                    <option value="ile" {{ old('geography_type') === 'ile' ? 'selected' : '' }}>Ile</option>
                    <option value="ville" {{ old('geography_type') === 'ville' ? 'selected' : '' }}>Ville</option>
                    <option value="pays" {{ old('geography_type', 'pays') === 'pays' ? 'selected' : '' }}>Pays</option>
                    <option value="continent" {{ old('geography_type') === 'continent' ? 'selected' : '' }}>Continent</option>
                    <option value="region" {{ old('geography_type') === 'region' ? 'selected' : '' }}>Region</option>
                    <option value="royaume" {{ old('geography_type') === 'royaume' ? 'selected' : '' }}>Royaume</option>
                    <option value="empire" {{ old('geography_type') === 'empire' ? 'selected' : '' }}>Empire</option>
                    <option value="federation" {{ old('geography_type') === 'federation' ? 'selected' : '' }}>Federation</option>
                    <option value="republique" {{ old('geography_type') === 'republique' ? 'selected' : '' }}>Republique</option>
                    <option value="colonie" {{ old('geography_type') === 'colonie' ? 'selected' : '' }}>Colonie</option>
                    <option value="planete" {{ old('geography_type') === 'planete' ? 'selected' : '' }}>Planete</option>
                    <option value="systeme" {{ old('geography_type') === 'systeme' ? 'selected' : '' }}>Systeme</option>
                    <option value="galaxie" {{ old('geography_type') === 'galaxie' ? 'selected' : '' }}>Galaxie</option>
                    <option value="dimension" {{ old('geography_type') === 'dimension' ? 'selected' : '' }}>Dimension</option>
                </select>
            </div>
            <div class="field">
                <label>Description du monde</label>
                <textarea name="summary" rows="6" placeholder="Histoire, ambiance, regles, culture...">{{ old('summary') }}</textarea>
            </div>
            <div class="field">
                <label>Carte du monde (image)</label>
                <input type="file" name="map" accept="image/*">
            </div>
            <div class="stack">
                <button class="btn" type="submit">Créer</button>
                <a class="btn secondary" href="{{ route('manage.worlds.index') }}">Annuler</a>
            </div>
        </form>
    </section>
@endsection
