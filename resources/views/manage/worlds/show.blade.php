@extends('manage.layout')

@section('title', 'Gestion - Monde')
@section('header', $world->name)

@section('content')
    <section class="panel">
        <p><strong>Nom:</strong> {{ $world->name }}</p>
        <p>
            <strong>Description:</strong><br>
            @if($world->summary)
                {!! nl2br(e($world->summary)) !!}
            @else
                <span class="muted">Aucune description.</span>
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
