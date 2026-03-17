@extends('manage.layout')

@section('title', 'Gestion - Nouvelle espèce')
@section('header', 'Nouvelle espèce')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.species.store') }}">
            @csrf

            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>

            <div class="field">
                <label>Nom</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="field">
                <label>Caractéristiques</label>
                <textarea name="characteristics">{{ old('characteristics') }}</textarea>
            </div>

            <div class="field">
                <label>Capacités</label>
                <textarea name="abilities">{{ old('abilities') }}</textarea>
            </div>

            <div class="field">
                <label>Durée de vie</label>
                <input type="text" name="lifespan" value="{{ old('lifespan') }}">
            </div>

            <div class="field">
                <label>Origine</label>
                <textarea name="origin">{{ old('origin') }}</textarea>
            </div>

            <div class="stack">
                <button class="btn" type="submit">Créer</button>
                <a class="btn secondary" href="{{ route('manage.species.index') }}">Annuler</a>
            </div>
        </form>
    </section>
@endsection
