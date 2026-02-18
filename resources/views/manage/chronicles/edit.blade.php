@extends('manage.layout')

@section('title', 'Gestion - Éditer chronique')
@section('header', 'Éditer chronique')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.chronicles.update', $chronicle) }}">
            @csrf @method('PUT')
            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>
            <div class="field">
                <label>Titre</label>
                <input type="text" name="title" value="{{ old('title', $chronicle->title) }}" required>
            </div>
            <div class="field">
                <label>Date d'événement</label>
                <input type="date" name="event_date" value="{{ old('event_date', optional($chronicle->event_date)->format('Y-m-d')) }}">
            </div>
            <div class="field">
                <label>Statut</label>
                <select name="status">
                    @foreach(['draft','published','archived'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $chronicle->status) === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Résumé</label>
                <textarea name="summary">{{ old('summary', $chronicle->summary) }}</textarea>
            </div>
            <div class="field">
                <label>Contenu</label>
                <textarea name="content">{{ old('content', $chronicle->content) }}</textarea>
            </div>
            <div class="stack">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('manage.chronicles.index') }}">Retour</a>
            </div>
        </form>
    </section>
@endsection
