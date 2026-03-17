@extends('manage.layout')

@section('title', 'Gestion - Éditer Lore')
@section('header', 'Éditer Lore')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.lore.update', $entry) }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>

            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Titre</label>
                    <input type="text" name="title" value="{{ old('title', $entry->title) }}" required>
                </div>
                <div class="field" style="grid-column: span 2;">
                    <label>Catégorie</label>
                    <select name="category">
                        <option value="">-</option>
                        @foreach($categoryOptions as $opt)
                            <option value="{{ $opt }}" {{ old('category', $entry->category) === $opt ? 'selected' : '' }}>{{ ucfirst($opt) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Contenu</label>
                <textarea name="content" rows="12">{{ old('content', $entry->content) }}</textarea>
            </div>

            <div class="field">
                <label>Tags (séparés par des virgules)</label>
                <input type="text" name="tags" value="{{ old('tags', $entry->tags) }}" placeholder="religion, ancien, interdit">
            </div>

            <div class="stack">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('manage.lore.index') }}">Retour</a>
            </div>
        </form>
    </section>
@endsection
