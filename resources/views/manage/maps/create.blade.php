@extends('manage.layout')

@section('title', 'Gestion - Nouvelle carte imaginaire')
@section('header', 'Nouvelle carte imaginaire')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.maps.store') }}">
            @csrf
            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>
            <div class="field">
                <label>Titre</label>
                <input type="text" name="title" value="{{ old('title') }}" required>
            </div>
            <div class="field">
                <label>Type de carte</label>
                <input type="text" name="map_type" value="{{ old('map_type') }}" placeholder="continent, region, ville...">
            </div>
            <div class="field">
                <label>URL image</label>
                <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://...">
            </div>
            <div class="field">
                <label>Statut</label>
                <select name="status">
                    <option value="draft">draft</option>
                    <option value="published">published</option>
                    <option value="archived">archived</option>
                </select>
            </div>
            <div class="field">
                <label>Description</label>
                <textarea name="description">{{ old('description') }}</textarea>
            </div>
            <div class="stack">
                <button class="btn" type="submit">Créer</button>
                <a class="btn secondary" href="{{ route('manage.maps.index') }}">Annuler</a>
            </div>
        </form>
    </section>
@endsection
