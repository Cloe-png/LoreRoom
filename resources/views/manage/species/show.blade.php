@extends('manage.layout')

@section('title', 'Gestion - Espèce')
@section('header', $species->name)

@section('content')
    <style>
        .species-section { margin-top: 10px; }
        .species-section h3 { margin: 0 0 6px; }
        .species-list { margin: 0; padding-left: 18px; }
    </style>

    <div class="stack" style="justify-content: space-between;">
        <div class="muted">Monde: {{ optional($species->world)->name ?: 'Monde actif' }}</div>
        <div class="stack">
            <a class="btn secondary" href="{{ route('manage.species.edit', $species) }}">Éditer</a>
            <form class="inline" method="POST" action="{{ route('manage.species.destroy', $species) }}">
                @csrf @method('DELETE')
                <button class="btn danger" type="submit">Supprimer</button>
            </form>
        </div>
    </div>

    <section class="panel species-section">
        <h3>Caractéristiques</h3>
        <p class="muted">{{ $species->characteristics ?: '-' }}</p>
    </section>

    <section class="panel species-section">
        <h3>Capacités</h3>
        <p class="muted">{{ $species->abilities ?: '-' }}</p>
    </section>

    <section class="panel species-section">
        <h3>Durée de vie</h3>
        <p class="muted">{{ $species->lifespan ?: '-' }}</p>
    </section>

    <section class="panel species-section">
        <h3>Origine</h3>
        <p class="muted">{{ $species->origin ?: '-' }}</p>
    </section>

    <section class="panel species-section">
        <h3>Personnages liés</h3>
        @if($species->characters->isEmpty())
            <p class="muted">Aucun personnage lié.</p>
        @else
            <ul class="species-list">
                @foreach($species->characters as $character)
                    <li><a href="{{ route('manage.characters.show', $character) }}">{{ $character->display_name }}</a></li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection
