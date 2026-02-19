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
                <label>Date evenement</label>
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
                <label>Resume</label>
                <textarea name="summary">{{ old('summary') }}</textarea>
            </div>
            <div class="field">
                <label>Contenu</label>
                <textarea name="content">{{ old('content') }}</textarea>
            </div>
            <div class="field">
                <label>Personnages lies</label>
                <select name="linked_character_ids[]" multiple size="8">
                    @foreach($characters as $character)
                        <option value="{{ $character->id }}" @selected(collect(old('linked_character_ids', []))->contains($character->id))>
                            {{ $character->display_name }}{{ $character->birth_date ? ' (' . $character->birth_date->format('Y-m-d') . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                <p class="muted">Maintiens Ctrl (ou Cmd) pour selectionner plusieurs personnages.</p>
            </div>
            <div class="stack">
                <button class="btn" type="submit">Creer</button>
                <a class="btn secondary" href="{{ route('manage.chronicles.index') }}">Annuler</a>
            </div>
        </form>
    </section>
@endsection
