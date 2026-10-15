@extends('manage.layout')

@section('title', 'Gestion - Chronique')
@section('header', $chronicle->title)

@section('content')
    <section class="panel">
        <p><strong>Monde:</strong> {{ optional($chronicle->world)->name }}</p>
        <p><strong>Date:</strong> {{ optional($chronicle->event_date)->format('d/m/Y') ?: 'Non renseignée' }}</p>
        <p><strong>Date de fin:</strong> {{ optional($chronicle->end_date)->format('d/m/Y') ?: 'Non renseignée' }}</p>
        <p><strong>Lieu:</strong> {{ optional($chronicle->eventPlace)->name ?: ($chronicle->event_location ?: 'Non renseigné') }}</p>
        <p><strong>Résumé:</strong><br>{{ $chronicle->summary ?: 'Aucun résumé.' }}</p>
        <p><strong>Contenu:</strong><br>{{ $chronicle->content ?: 'Aucun contenu.' }}</p>
        @if($chronicle->places->isNotEmpty())<section class="panel"><h2>Lieux liés</h2>@foreach($chronicle->places as $place)<p><a href="{{ route('manage.places.show',$place) }}">{{ $place->name }}</a></p>@endforeach</section>@endif
        <div class="stack">
            <a class="btn secondary" href="{{ route('manage.chronicles.edit', $chronicle) }}">Éditer</a>
            <a class="btn secondary" href="{{ route('manage.chronicles.index') }}">Retour</a>
        </div>
    </section>
@endsection

