@extends('manage.layout')

@section('title', 'Gestion - Monde')
@section('header', $world->name)

@section('content')
    <section class="panel">
        <p><strong>Nom:</strong> {{ $world->name }}</p>
        <p><strong>Type:</strong> {{ ucfirst($world->geography_type ?? 'pays') }}</p>
        <p><strong>Carte:</strong>
            @if($world->map_path)
                <a class="btn secondary" href="{{ asset('storage/'.$world->map_path) }}" target="_blank" rel="noopener">Ouvrir la carte</a>
            @else
                Aucune carte.
            @endif
        </p>
        <p class="muted">
            Personnages: {{ $world->characters->count() }} |
            Lieux: {{ $world->places->count() }} |
            Chroniques: {{ $world->chronicles->count() }}
        </p>
        <div class="stack">
            <a class="btn secondary" href="{{ route('manage.worlds.edit', $world) }}">Éditer</a>
            <a class="btn secondary" href="{{ route('manage.worlds.index') }}">Retour</a>
        </div>
    </section>
@endsection
