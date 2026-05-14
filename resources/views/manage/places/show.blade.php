@extends('manage.layout')

@section('title', 'Gestion - Lieu')
@section('header', $place->name)

@section('content')
    <section class="panel">
        <p><strong>Monde:</strong> {{ optional($place->world)->name }}</p>
        <p><strong>Type:</strong> {{ $place->type ? ucfirst($place->type) : 'Non défini' }}</p>
        <p><strong>Région:</strong> {{ $place->region ?: 'Non définie' }}</p>

        @if($place->image_path)
            <p><strong>Image principale:</strong></p>
            <img src="{{ route('media.show', ['path' => $place->image_path]) }}" alt="Image du lieu" style="max-width:360px; border-radius:10px; border:1px solid rgba(101,74,42,.3);">
        @endif

        <p><strong>Résumé :</strong><br>{{ $place->summary ?: 'Aucun résumé.' }}</p>

        <div class="panel" style="margin-top:14px;">
            <strong>Stats d'usage</strong>
            <p class="muted" style="margin-top:6px;">
                Chroniques liees: {{ $eventChroniclesCount }} |
                Naissances: {{ $birthCharactersCount }} |
                Residences: {{ $residentCharactersCount }}
            </p>

            @if($place->eventChronicles->isNotEmpty())
                <p><strong>Dernières chroniques :</strong></p>
                <ul>
                    @foreach($place->eventChronicles as $chronicle)
                        <li>
                            <a href="{{ route('manage.chronicles.show', $chronicle) }}">{{ $chronicle->title }}</a>
                            <span class="muted">({{ optional($chronicle->event_date)->format('Y-m-d') ?: '-' }})</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if($place->birthCharacters->isNotEmpty())
                <p><strong>Personnages nes ici:</strong></p>
                <ul>
                    @foreach($place->birthCharacters as $character)
                        <li><a href="{{ route('manage.characters.show', $character) }}">{{ $character->display_name }}</a></li>
                    @endforeach
                </ul>
            @endif

            @if($place->residentCharacters->isNotEmpty())
                <p><strong>Personnages residant ici:</strong></p>
                <ul>
                    @foreach($place->residentCharacters as $character)
                        <li><a href="{{ route('manage.characters.show', $character) }}">{{ $character->display_name }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="panel" style="margin-top:14px;">
            <strong>Galerie</strong>
            @if($place->galleryImages->isEmpty())
                <p class="muted">Aucune image de galerie.</p>
            @else
                <div class="grid-4" style="margin-top:10px;">
                    @foreach($place->galleryImages as $img)
                        <div class="card" style="padding:8px;">
                            <img src="{{ route('media.show', ['path' => $img->image_path]) }}" alt="Galerie lieu" style="width:100%; border-radius:6px; margin-bottom:6px;">
                            <div class="muted">{{ $img->caption ?: 'Sans legende' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="stack" style="margin-top:12px;">
            <a class="btn secondary" href="{{ route('manage.places.edit', $place) }}">Éditer</a>
            <a class="btn secondary" href="{{ route('manage.places.index') }}">Retour</a>
        </div>
    </section>
@endsection
