@extends('manage.layout')

@section('title', 'Gestion - Carte imaginaire')
@section('header', $map->title)

@section('content')
    <section class="panel">
        <p><strong>Monde:</strong> {{ optional($map->world)->name }}</p>
        <p><strong>Type:</strong> {{ $map->map_type ?: 'Non défini' }}</p>
        <p><strong>Statut:</strong> {{ $map->status }}</p>
        @if($map->image_url)
            <p><strong>Image:</strong> <a class="chip" href="{{ $map->image_url }}" target="_blank" rel="noopener">Ouvrir</a></p>
        @endif
        <p><strong>Description:</strong><br>{{ $map->description ?: 'Aucune description.' }}</p>
        <div class="stack">
            <a class="btn secondary" href="{{ route('manage.maps.edit', $map) }}">Éditer</a>
            <a class="btn secondary" href="{{ route('manage.maps.index') }}">Retour</a>
        </div>
    </section>
@endsection
