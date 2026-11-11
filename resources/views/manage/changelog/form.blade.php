@extends('manage.layout')
@section('title', $entry ? 'Modifier une nouveauté' : 'Nouvelle nouveauté')
@section('header', $entry ? 'Modifier une nouveauté' : 'Nouvelle nouveauté')
@section('content')
<form method="POST" action="{{ $entry ? route('manage.nouveautes.update', $entry->id) : route('manage.nouveautes.store') }}" class="home-card">
    @csrf @if($entry) @method('PUT') @endif
    <label for="title">Titre</label><input id="title" name="title" maxlength="80" required value="{{ old('title', $entry->title ?? '') }}">
    <label for="body">Texte</label><textarea id="body" name="body" maxlength="280" required>{{ old('body', $entry->body ?? '') }}</textarea>
    <label><input type="checkbox" name="is_published" value="1" {{ old('is_published', $entry->is_published ?? false) ? 'checked' : '' }}> Publiée</label>
    <button class="home-btn" type="submit">Enregistrer</button>
</form>
@endsection
