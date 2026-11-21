@extends('manage.layout')

@section('title', 'Salle de ' . $character->display_name)
@section('header', 'Musée')

@section('content')
<div id="museum-room" class="mus-gallery" data-gallery data-images="{{ json_encode($images->items()) }}" data-characters="{{ json_encode($characters->map(fn($person) => ['id' => $person->id, 'name' => $person->display_name, 'first_name' => $person->first_name ?: $person->display_name])) }}" data-can-manage="{{ $canManage ? '1' : '0' }}" data-csrf="{{ csrf_token() }}">
    <div class="mus-gallery-head mus-gh">
        <div>
            <h1 class="mus-gt">Salle de {{ $character->display_name }}
                <small>
                    {{ $images->total() }} {{ $images->total() === 1 ? 'image' : 'images' }}
                @if($mentionCount > 0)
                    · dont {{ $mentionCount }} où {{ $character->first_name ?: $character->display_name }} est mentionné(e) sur la photo d'un autre
                @endif
                </small>
            </h1>
        </div>
        @if($character->portrait_url)
            <img class="mus-gallery-medallion mus-gav" src="{{ $character->portrait_url }}" alt="Portrait de {{ $character->display_name }}">
        @else
            <span class="mus-gallery-medallion mus-gallery-initial mus-gav" aria-label="{{ $character->display_name }}">{{ mb_substr($character->display_name, 0, 1) }}</span>
        @endif
        <nav>
            @if($canManage)<button class="mus-button" type="button" data-open-upload>+ Ajouter une image</button>@endif
        </nav>
    </div>

    @if($images->isEmpty())
        <p class="mus-gallery-empty">Aucune image pour l'instant.</p>
    @else
        <ul class="mus-gallery-wall mus-wall" aria-label="Images de la galerie">
        @foreach($images as $image)
            <li class="mus-gallery-listitem">
                <figure class="mus-gi mus-gallery-card{{ $image['is_mention'] ? ' m' : '' }}">
                    <button type="button" class="mus-gallery-image" data-open-image="{{ $image['id'] }}" aria-label="Ouvrir {{ $image['title'] }}">
                        <span class="mus-fx mus-gallery-frame" data-frame>
                            <span class="mus-art"><img src="{{ $image['thumb_url'] }}" alt="{{ $image['alt'] }}" loading="lazy"></span>
                            <img class="mus-cd" src="{{ asset('images/museum/cadre.png') }}" alt="" aria-hidden="true">
                        </span>
                    </button>
                    <figcaption class="mus-gallery-caption">
                        <strong>{{ $image['title'] }}</strong><br>
                        <span class="mus-tg2">@if($image['mentions']->isNotEmpty()) Avec {{ $image['mentions']->pluck('name')->join(', ') }} @else Photo de {{ $image['owner'] }} @endif</span>
                    </figcaption>
                </figure>
            </li>
        @endforeach
        </ul>
        <div class="mus-gallery-pagination">{{ $images->links() }}</div>
    @endif

    <div id="museum-lb" class="mus-gallery-dialog" data-viewer hidden role="dialog" aria-modal="true" aria-labelledby="mus-viewer-title" tabindex="-1">
        <button type="button" class="mus-gallery-close" data-close aria-label="Fermer">Fermer</button>
        <div class="mus-lbi">
            <button type="button" data-step="-1" class="mus-gallery-step" aria-label="Image précédente">←</button>
            <span class="mus-fx mus-gallery-frame" data-viewer-frame>
                <span class="mus-art"></span>
                <img class="mus-cd" src="{{ asset('images/museum/cadre.png') }}" alt="" aria-hidden="true">
            </span>
            <section class="mus-pan mus-gallery-view-content"></section>
            <button type="button" data-step="1" class="mus-gallery-step" aria-label="Image suivante">→</button>
        </div>
    </div>

    @if($canManage)
    <div id="museum-ad" class="mus-gallery-dialog" data-uploader hidden role="dialog" aria-modal="true" aria-labelledby="mus-upload-title" tabindex="-1">
        <form class="mus-pan mus-gallery-upload" method="post" enctype="multipart/form-data" action="{{ route('manage.character-gallery.store', $character) }}">
            @csrf
            <button type="button" class="mus-gallery-close" data-close aria-label="Fermer">Fermer</button>
            <h2 id="mus-upload-title">Ajouter une image à la galerie</h2>
            <label>Titre <input name="title" maxlength="60" required></label>
            <label>Image (JPG, PNG ou WebP) <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required></label>
            <label>Description <textarea name="description" maxlength="280" rows="3"></textarea></label>
            <fieldset>
                <legend>Personnages mentionnés</legend>
                <div class="mus-mention-picker" data-mention-picker data-multiple="1">
                    <label>Ajouter une mention <input type="search" data-mention-query autocomplete="off" placeholder="@prénom" aria-autocomplete="list" aria-label="Rechercher un personnage avec @"></label>
                    <div class="mus-mention-results" data-mention-results role="listbox" hidden></div>
                    <div class="mus-mention-selected" data-mention-selected aria-live="polite"></div>
                </div>
            </fieldset>
            <p class="mus-gallery-error" data-error role="alert"></p>
            <button class="mus-button" type="submit">Ajouter l'image</button>
        </form>
    </div>
    @endif
</div>
<link rel="stylesheet" href="{{ mix('css/museum.css') }}?v={{ filemtime(public_path('css/museum.css')) }}">
<script src="{{ mix('js/museum.js') }}?v={{ filemtime(public_path('js/museum.js')) }}" defer></script>
@endsection
