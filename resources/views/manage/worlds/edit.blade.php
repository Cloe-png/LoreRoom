@extends('manage.layout')

@section('title', 'Gestion - Éditer monde')
@section('header', 'Éditer monde')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.worlds.update', $world) }}">
            @csrf @method('PUT')
            <div class="field">
                <label>Nom</label>
                <input type="text" name="name" value="{{ old('name', $world->name) }}" required>
            </div>
            <div class="field">
                <label>Description du monde</label>
                <textarea name="summary" rows="6" placeholder="Histoire, ambiance, règles, culture...">{{ old('summary', $world->summary) }}</textarea>
            </div>
            <div class="stack">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('manage.worlds.index') }}">Retour</a>
            </div>
        </form>
    </section>
@endsection
