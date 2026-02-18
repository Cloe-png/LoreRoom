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
                    <option value="ile" @selected(old('geography_type') === 'ile')>Ile</option>
                    <option value="ville" @selected(old('geography_type') === 'ville')>Ville</option>
                    <option value="pays" @selected(old('geography_type', 'pays') === 'pays')>Pays</option>
                </select>
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
