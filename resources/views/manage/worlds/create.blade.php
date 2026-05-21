@extends('manage.layout')

@section('title', 'Gestion - Nouveau monde')
@section('header', 'Nouveau monde')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.worlds.store') }}">
            @csrf
            @if(($importableWorlds ?? collect())->isNotEmpty())
                <div class="field">
                    <label>Importer depuis un univers existant</label>
                    <select name="source_world_id">
                        <option value="">Aucun, partir de zéro</option>
                        @foreach($importableWorlds as $world)
                            <option value="{{ $world->id }}" {{ (string) old('source_world_id') === (string) $world->id ? 'selected' : '' }}>
                                {{ $world->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="muted" style="margin:6px 0 0;">Si tu choisis un univers source, le nouveau monde reprendra ses personnages, lieux, chroniques, factions, lore et liens associés.</p>
                </div>
            @endif
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
