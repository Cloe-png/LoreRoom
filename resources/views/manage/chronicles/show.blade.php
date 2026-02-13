@extends('manage.layout')

@section('title', 'Gestion - Chronique')
@section('header', $chronicle->title)

@section('content')
    <section class="panel">
        <p><strong>Monde:</strong> {{ optional($chronicle->world)->name }}</p>
        <p><strong>Date:</strong> {{ optional($chronicle->event_date)->format('Y-m-d') ?: 'Non renseignee' }}</p>
        <p><strong>Statut:</strong> {{ $chronicle->status }}</p>
        <p><strong>Résumé:</strong><br>{{ $chronicle->summary ?: 'Aucun résumé.' }}</p>
        <p><strong>Contenu:</strong><br>{{ $chronicle->content ?: 'Aucun contenu.' }}</p>
        <div class="stack">
            <a class="btn secondary" href="{{ route('manage.chronicles.edit', $chronicle) }}">Éditer</a>
            <a class="btn secondary" href="{{ route('manage.chronicles.index') }}">Retour</a>
        </div>
    </section>
@endsection
