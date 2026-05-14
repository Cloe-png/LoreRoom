@extends('manage.layout')

@section('title', 'Gestion - Nouveau monde')
@section('header', 'Nouveau monde')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.worlds.store') }}">
            @csrf
            <div class="field">
                <label>Nom</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="field">
                <label>Description du monde</label>
                <textarea name="summary" rows="6" placeholder="Histoire, ambiance, règles, culture...">{{ old('summary') }}</textarea>
            </div>
            <div class="stack">
                <button class="btn" type="submit">Créer</button>
                <a class="btn secondary" href="{{ route('manage.worlds.index') }}">Annuler</a>
            </div>
        </form>
    </section>
@endsection
