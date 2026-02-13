@extends('manage.layout')

@section('title', 'Gestion - Nouvelle chronique')
@section('header', 'Nouvelle chronique')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.chronicles.store') }}">
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
                <label>Date d'événement</label>
                <input type="date" name="event_date" value="{{ old('event_date') }}">
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
                <label>Résumé</label>
                <textarea name="summary">{{ old('summary') }}</textarea>
            </div>
            <div class="field">
                <label>Contenu</label>
                <textarea name="content">{{ old('content') }}</textarea>
            </div>
            <div class="stack">
                <button class="btn" type="submit">Créer</button>
                <a class="btn secondary" href="{{ route('manage.chronicles.index') }}">Annuler</a>
            </div>
        </form>
    </section>
@endsection
