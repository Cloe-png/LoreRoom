<!doctype html>
<html lang="fr" data-theme="{{ auth()->check() ? (auth()->user()->theme ?? 'dark') : (in_array(request()->cookie('theme'), ['dark','light','auto'], true) ? request()->cookie('theme') : 'dark') }}">
@php
    $artworks = __('landing.artworks');
    $landingFigures = [];
    foreach ($artworks as $index => $artwork) {
        $image = 'images/landing/salle-' . ($index + 1) . '.webp';
        $landingFigures[] = array_merge($artwork, [
            'image' => $image,
            'available' => file_exists(public_path($image)),
        ]);
    }
    $landingMascot = 'images/mascot/tour-regard.png';
    $showLandingMascot = config('landing.mascot', false) && file_exists(public_path($landingMascot));
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ (auth()->check() ? auth()->user()->theme : request()->cookie('theme')) === 'light' ? '#efe2c9' : '#111723' }}" data-theme-color>
    <link rel="icon" type="image/png" href="{{ asset('images/brand/loreroom.png') }}?v=1">
    <link rel="apple-touch-icon" href="{{ asset('images/brand/loreroom.png') }}?v=1">
    <title>{{ __('app.public.start_page_title') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ mix('css/landing.css') }}">
    <script>
        window.landingTheme = {
            authenticated: @json(auth()->check()),
            preferenceUrl: @json(auth()->check() ? route('manage.preferences.theme') : null),
            csrf: @json(csrf_token())
        };
    </script>
</head>
<body class="lp-page">
    <div class="lp-shell">
        <header class="lp-header lp-reveal">
            <a class="lp-brand" href="{{ url('/') }}" aria-label="LoreRoom, accueil">LOREROOM</a>
            <nav class="lp-header-actions" aria-label="Navigation principale">
                @guest
                    <a class="lp-login" href="{{ route('login') }}">Se connecter</a>
                @endguest
                @include('partials.theme-toggle')
            </nav>
        </header>

        <main class="lp-main">
            <section class="lp-intro" aria-labelledby="lp-title">
                <p class="lp-kicker lp-reveal">Le musée de tes histoires</p>
                <h1 class="lp-title lp-reveal" id="lp-title">Chaque monde garde<br><em>une histoire.</em></h1>
                <p class="lp-description lp-reveal">Retrouve tes personnages, leurs récits et les souvenirs qui donnent vie à tes univers.</p>
                <div class="lp-actions lp-reveal">
                    @auth
                        <a class="lp-button lp-button--primary" href="{{ route('manage.index') }}">Retourner à mon carnet
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h15m-6-6 6 6-6 6"/></svg>
                        </a>
                    @else
                        <a class="lp-button lp-button--primary" href="{{ route('login') }}">Entrer
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h15m-6-6 6 6-6 6"/></svg>
                        </a>
                        <a class="lp-button lp-button--secondary" href="{{ route('register') }}">Créer un compte</a>
                    @endauth
                </div>
                @guest
                    <p class="lp-resume lp-reveal">Déjà un carnet ? <a href="{{ route('login') }}">Reprends où tu t'étais arrêté.</a></p>
                @endguest
            </section>

            <section class="lp-salle lp-reveal" aria-label="Une salle du musée" aria-hidden="true">
                <div class="lp-wall"></div>
                <div class="lp-floor"></div>
                <figure class="lp-picture lp-picture--left">
                    <div class="lp-frame">
                        <div class="lp-art-window">
                            @if($landingFigures[0]['available'])
                                <img class="lp-side-art" src="{{ asset($landingFigures[0]['image']) }}" alt="" loading="lazy">
                            @else
                                <div class="lp-silhouette lp-silhouette--aria"><span>A</span></div>
                            @endif
                        </div>
                        <img class="lp-frame-overlay" src="{{ asset('images/museum/cadre.png') }}" alt="" draggable="false">
                    </div>
                </figure>
                <figure class="lp-picture lp-picture--right">
                    <div class="lp-frame">
                        <div class="lp-art-window">
                            @if($landingFigures[2]['available'])
                                <img class="lp-side-art" src="{{ asset($landingFigures[2]['image']) }}" alt="" loading="lazy">
                            @else
                                <div class="lp-silhouette lp-silhouette--noor"><span>N</span></div>
                            @endif
                        </div>
                        <img class="lp-frame-overlay" src="{{ asset('images/museum/cadre.png') }}" alt="" draggable="false">
                    </div>
                </figure>
                <figure class="lp-picture lp-picture--center">
                    <div class="lp-frame">
                        <div class="lp-art-window" data-landing-carousel>
                            @foreach($landingFigures as $index => $artwork)
                                <div class="lp-slide{{ $index === 0 ? ' is-active' : '' }}" data-title="{{ $artwork['title'] }}" data-room="{{ $artwork['room'] }}">
                                    @if($artwork['available'])
                                        <img src="{{ asset($artwork['image']) }}" alt="" @if($index > 0) loading="lazy" @endif>
                                    @else
                                        <div class="lp-silhouette lp-silhouette--{{ strtolower($artwork['name']) }}"><span>{{ $artwork['initial'] }}</span><small>{{ $artwork['name'] }}</small></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <img class="lp-frame-overlay" src="{{ asset('images/museum/cadre.png') }}" alt="" draggable="false">
                    </div>
                    <figcaption class="lp-plaque">
                        <strong data-plaque-title>{{ $landingFigures[0]['title'] }}</strong>
                        <span data-plaque-room>{{ $landingFigures[0]['room'] }}</span>
                    </figcaption>
                </figure>
            </section>
        </main>

        <section class="lp-benefits" aria-label="Ce que tu peux créer">
            <article class="lp-benefit lp-reveal">
                <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="10" r="5"/><path d="M6 27c.8-5.1 4.4-8 10-8s9.2 2.9 10 8M5 9l2 2m20-2-2 2M16 2v2"/></svg>
                <div><h2>Des personnages vivants</h2><p>Des portraits, des liens et des souvenirs qui leur ressemblent.</p></div>
            </article>
            <article class="lp-benefit lp-reveal">
                <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M6 5.5h12a4 4 0 0 1 4 4v17H10a4 4 0 0 1-4-4zM22 9.5a4 4 0 0 1 4-4h1v17a4 4 0 0 1-4 4h-1M10 12h7m-7 5h7"/></svg>
                <div><h2>Des récits en chapitres</h2><p>Écris leurs aventures au fil des pages.</p></div>
            </article>
            <article class="lp-benefit lp-reveal">
                <svg viewBox="0 0 32 32" aria-hidden="true"><path d="m16 3 11 6.5v13L16 29 5 22.5v-13zM5 9.5l11 6.7 11-6.7M16 16.2V29M10 6.5l11 6.8"/></svg>
                <div><h2>Des univers cohérents</h2><p>Garde lieux, peuples et histoires réunis.</p></div>
            </article>
        </section>

        <footer class="lp-footer lp-reveal">Un carnet pour chaque univers.</footer>
    </div>
    @if($showLandingMascot)
        <img class="lp-mascot" src="{{ asset($landingMascot) }}" alt="" aria-hidden="true">
    @endif
    <script src="{{ mix('js/landing.js') }}" defer></script>
</body>
</html>
