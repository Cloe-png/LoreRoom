@extends('manage.layout')

@section('title', 'Gestion - Nouveau métier')
@section('header', 'Nouveau métier')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.jobs.store') }}">
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
                <label>Description</label>
                <textarea name="description">{{ old('description') }}</textarea>
            </div>

            <div class="stack">
                <button class="btn" type="submit">Créer</button>
                <a class="btn secondary" href="{{ route('manage.jobs.index') }}">Annuler</a>
            </div>
        </form>
    </section>
@endsection
