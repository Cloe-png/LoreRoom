@extends('manage.layout')

@section('title', 'Gestion - Éditer lieu')
@section('header', 'Éditer lieu')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.places.update', $place) }}">
            @csrf @method('PUT')
            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>
            <div class="field">
                <label>Nom</label>
                <input type="text" name="name" value="{{ old('name', $place->name) }}" required>
            </div>
            <div class="field">
                <label>Region</label>
                <input type="text" name="region" value="{{ old('region', $place->region) }}">
            </div>
            <div class="field">
                <label>Résumé</label>
                <textarea name="summary">{{ old('summary', $place->summary) }}</textarea>
            </div>
            <div class="stack">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('manage.places.index') }}">Retour</a>
            </div>
        </form>
    </section>
@endsection
