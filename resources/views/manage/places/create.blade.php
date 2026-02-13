@extends('manage.layout')

@section('title', 'Gestion - Nouveau lieu')
@section('header', 'Nouveau lieu')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.places.store') }}">
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
                <label>Region</label>
                <input type="text" name="region" value="{{ old('region') }}">
            </div>
            <div class="field">
                <label>Résumé</label>
                <textarea name="summary">{{ old('summary') }}</textarea>
            </div>
            <div class="stack">
                <button class="btn" type="submit">Créer</button>
                <a class="btn secondary" href="{{ route('manage.places.index') }}">Annuler</a>
            </div>
        </form>
    </section>
@endsection
