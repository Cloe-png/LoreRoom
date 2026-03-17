@extends('manage.layout')

@section('title', 'Gestion - Éditer espèce')
@section('header', 'Éditer espèce')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.species.update', $species) }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>

            <div class="field">
                <label>Nom</label>
                <input type="text" name="name" value="{{ old('name', $species->name) }}" required>
            </div>

            <div class="field">
                <label>Caractéristiques</label>
                <textarea name="characteristics">{{ old('characteristics', $species->characteristics) }}</textarea>
            </div>

            <div class="field">
                <label>Capacités</label>
                <textarea name="abilities">{{ old('abilities', $species->abilities) }}</textarea>
            </div>

            <div class="field">
                <label>Durée de vie</label>
                <input type="text" name="lifespan" value="{{ old('lifespan', $species->lifespan) }}">
            </div>

            <div class="field">
                <label>Origine</label>
                <textarea name="origin">{{ old('origin', $species->origin) }}</textarea>
            </div>

            <div class="stack">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('manage.species.index') }}">Retour</a>
            </div>
        </form>
    </section>
@endsection
