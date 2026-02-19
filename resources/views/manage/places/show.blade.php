@extends('manage.layout')

@section('title', 'Gestion - Lieu')
@section('header', $place->name)

@section('content')
    <section class="panel">
        <p><strong>Monde:</strong> {{ optional($place->world)->name }}</p>
        <p><strong>Région:</strong> {{ $place->region ?: 'Non définie' }}</p>
        <p><strong>Résumé:</strong><br>{{ $place->summary ?: 'Aucun résumé.' }}</p>
        <div class="stack">
            <a class="btn secondary" href="{{ route('manage.places.edit', $place) }}">Éditer</a>
            <a class="btn secondary" href="{{ route('manage.places.index') }}">Retour</a>
        </div>
    </section>
@endsection
