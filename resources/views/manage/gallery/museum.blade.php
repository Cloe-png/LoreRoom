@extends('manage.layout')

@section('title', 'Musée — ' . ($activeWorld->name ?? 'LoreRoom'))
@section('header', 'Musée')

@section('content')
<div class="mus-page">
    <div class="mus-toolbar">
        <a class="mus-button mus-button--secondary" href="{{ route('manage.gallery.index', ['view' => 'grid']) }}">Vue en grille</a>
        @if($activeWorld)
            <form method="GET" action="{{ route('manage.gallery.index') }}" class="mus-filter">
                @if(request()->query('p'))<input type="hidden" name="p" value="{{ request()->query('p') }}">@endif
                <label><input type="checkbox" name="without_portrait" value="1" {{ $showMissing ? 'checked' : '' }} onchange="this.form.submit()"> <span>Afficher les personnages sans portrait</span></label>
            </form>
        @endif
    </div>

    @if(!$activeWorld)
        <section class="mus-empty"><h2>Le musée est fermé : crée d’abord un monde.</h2><a class="mus-button" href="{{ route('manage.worlds.create') }}">Créer un monde</a></section>
    @elseif($paintings->isEmpty())
        <section class="mus-empty"><h2>Aucun portrait à exposer pour l’instant.</h2><p>Ajoute un portrait à un personnage ou affiche les personnages sans portrait.</p></section>
    @else
        <section class="mus-room" role="region" aria-roledescription="carrousel" aria-label="Couloir des portraits du musée" tabindex="0"
            data-active-index="{{ $activeIndex }}" data-count="{{ $paintings->count() }}"
            data-frame-url="{{ asset('images/museum/cadre.png') }}" data-csrf-token="{{ csrf_token() }}">
            <div class="mus-wall" aria-hidden="true"></div>
            <div class="mus-floor" aria-hidden="true"></div>
            <div class="mus-stage" data-stage>
                @foreach($visiblePaintings as $painting)
                    @php($index = $paintings->search(fn($item) => $item['id'] === $painting['id']))
                    <article class="mus-slot" data-index="{{ $index }}" role="group" aria-roledescription="diapositive" aria-label="{{ $painting['name'] }}, {{ $index + 1 }} sur {{ $paintings->count() }}">
                        <figure class="mus-fr">
                            <button class="mus-art" type="button" aria-label="Afficher {{ $painting['name'] }}">
                                <img class="mus-portrait" src="{{ $painting['image_url'] }}" alt="Portrait de {{ $painting['name'] }}" loading="{{ $index === $activeIndex ? 'eager' : 'lazy' }}">
                                <span class="mus-silhouette" aria-hidden="true" hidden>{{ $painting['initials'] }}</span>
                            </button>
                            <img class="mus-cd" src="{{ asset('images/museum/cadre.png') }}" alt="" aria-hidden="true" draggable="false">
                        </figure>
                        <section class="mus-plaque">
                            <h2>{{ $painting['name'] }}</h2>
                            <p class="mus-dates">{{ trim(($painting['birth_year'] ?: '') . (($painting['birth_year'] || $painting['death_year']) ? ' — ' : '') . ($painting['death_year'] ?: '')) }}</p>
                            <p class="mus-description">{{ $painting['description'] ?: 'Aucune description pour l’instant.' }}</p>
                            <div class="mus-plaque-links"><a href="{{ $painting['sheet_url'] }}">Ouvrir la fiche</a> · <a href="{{ $painting['gallery_url'] }}">Sa galerie · {{ $painting['gallery_count'] }} images</a></div>
                        </section>
                    </article>
                @endforeach
            </div>

            <button type="button" class="mus-arrow mus-previous" data-previous aria-label="Portrait précédent" disabled>‹</button>
            <button type="button" class="mus-arrow mus-next" data-next aria-label="Portrait suivant" {{ $paintings->count() < 2 ? 'disabled' : '' }}>›</button>
            <div class="mus-controls">
                <div class="mus-miniframes" data-miniframes aria-label="Choisir un portrait">
                    @foreach($paintings as $index => $painting)
                        <button type="button" class="mus-mini-frame" data-jump="{{ $index }}" aria-label="Aller à {{ $painting['name'] }}" {{ $index === $activeIndex ? 'aria-current=true' : '' }}></button>
                    @endforeach
                </div>
                <span class="mus-counter" data-counter aria-live="off">{{ $activeIndex + 1 }} / {{ $paintings->count() }}</span>
                <button type="button" class="mus-tour" data-tour aria-pressed="false">Visite guidée</button>
            </div>
            <p class="mus-live" aria-live="polite" aria-atomic="true" data-live></p>
            <script type="application/json" data-paintings>@json($paintings)</script>
        </section>
    @endif
</div>
<script src="{{ mix('js/museum.js') }}?v={{ filemtime(public_path('js/museum.js')) }}" defer></script>
@endsection
