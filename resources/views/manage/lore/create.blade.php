@extends('manage.layout')

@section('title', 'Gestion - Nouvelle entrée Lore')
@section('header', 'Nouvelle entrée Lore')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.lore.store') }}">
            @csrf

            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>

            <div class="grid-4">
                <div class="field" style="grid-column: span 2;">
                    <label>Titre</label>
                    <input type="text" name="title" value="{{ old('title') }}" required>
                </div>
                <div class="field" style="grid-column: span 2;">
                    <label>Catégorie</label>
                    <select name="category">
                        <option value="">-</option>
                        @foreach($categoryOptions as $opt)
                            <option value="{{ $opt }}" {{ old('category') === $opt ? 'selected' : '' }}>{{ ucfirst($opt) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Contenu</label>
                <textarea name="content" rows="12">{{ old('content') }}</textarea>
            </div>

            <div class="field">
                <label>Tags (séparés par des virgules)</label>
                <input type="text" name="tags" value="{{ old('tags') }}" placeholder="religion, ancien, interdit">
            </div>

            <div class="stack">
                <button class="btn" type="submit">Créer</button>
                <a class="btn secondary" href="{{ route('manage.lore.index') }}">Annuler</a>
            </div>
        </form>
    </section>
@endsection
