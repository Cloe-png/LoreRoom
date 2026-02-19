@extends('manage.layout')

@section('title', 'Gestion - Editer chronique')
@section('header', 'Editer chronique')

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
                <label>Date evenement</label>
                <input type="date" name="event_date" value="{{ old('event_date', optional($chronicle->event_date)->format('Y-m-d')) }}">
            </div>
            <div class="field">
                <label>Statut</label>
                <select name="status">
                    @foreach(['draft', 'published', 'archived'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $chronicle->status) === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Resume</label>
                <textarea name="summary">{{ old('summary', $chronicle->summary) }}</textarea>
            </div>
            <div class="field">
                <label>Contenu</label>
                <textarea name="content">{{ old('content', $chronicle->content) }}</textarea>
            </div>
            @php
                $selectedLinked = collect(old('linked_character_ids', $chronicle->linkedCharacters->pluck('id')->all()))->map(fn ($id) => (int) $id);
            @endphp
            <div class="field">
                <label>Personnages lies</label>
                <select name="linked_character_ids[]" multiple size="8">
                    @foreach($characters as $character)
                        <option value="{{ $character->id }}" @selected($selectedLinked->contains((int) $character->id))>
                            {{ $character->display_name }}{{ $character->birth_date ? ' (' . $character->birth_date->format('Y-m-d') . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                <p class="muted">Maintiens Ctrl (ou Cmd) pour selectionner plusieurs personnages.</p>
            </div>
            <div class="stack">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('manage.chronicles.index') }}">Retour</a>
            </div>
        </form>
    </section>
@endsection
