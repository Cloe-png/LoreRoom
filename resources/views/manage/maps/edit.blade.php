@extends('manage.layout')

@section('title', 'Gestion - Éditer carte imaginaire')
@section('header', 'Éditer carte imaginaire')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.maps.update', $map) }}">
            @csrf @method('PUT')
            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>
            <div class="field">
                <label>Titre</label>
                <input type="text" name="title" value="{{ old('title', $map->title) }}" required>
            </div>
            <div class="field">
                <label>Type de carte</label>
                <input type="text" name="map_type" value="{{ old('map_type', $map->map_type) }}">
            </div>
            <div class="field">
                <label>URL image</label>
                <input type="url" name="image_url" value="{{ old('image_url', $map->image_url) }}">
            </div>
            <div class="field">
                <label>Statut</label>
                <select name="status">
                    @foreach(['draft','published','archived'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $map->status) === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Description</label>
                <textarea name="description">{{ old('description', $map->description) }}</textarea>
            </div>
            <div class="stack">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('manage.maps.index') }}">Retour</a>
            </div>
        </form>
    </section>
@endsection
