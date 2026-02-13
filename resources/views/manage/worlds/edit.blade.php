@extends('manage.layout')

@section('title', 'Gestion - Éditer monde')
@section('header', 'Éditer monde')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.worlds.update', $world) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="field">
                <label>Nom</label>
                <input type="text" name="name" value="{{ old('name', $world->name) }}" required>
            </div>
            <div class="field">
                <label>Type</label>
                <select name="geography_type" required>
                    <option value="ile" @selected(old('geography_type', $world->geography_type) === 'ile')>Ile</option>
                    <option value="ville" @selected(old('geography_type', $world->geography_type) === 'ville')>Ville</option>
                    <option value="pays" @selected(old('geography_type', $world->geography_type) === 'pays')>Pays</option>
                </select>
            </div>
            <div class="field">
                <label>Carte du monde (image)</label>
                <input type="file" name="map" accept="image/*">
                @if($world->map_path)
                    <p class="muted">Carte actuelle: {{ $world->map_path }}</p>
                @endif
            </div>
            <div class="stack">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('manage.worlds.index') }}">Retour</a>
            </div>
        </form>
    </section>
@endsection
