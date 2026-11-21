<!doctype html>
<html lang="fr" data-theme="{{ auth()->check() ? (auth()->user()->theme ?? 'dark') : (in_array(request()->cookie('theme'), ['dark','light','auto'], true) ? request()->cookie('theme') : 'dark') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111723" data-theme-color>
    <link rel="icon" type="image/png" href="{{ asset('loreroom.png') }}?v=1">
    <link rel="apple-touch-icon" href="{{ asset('loreroom.png') }}?v=1">
    <title>{{ __('app.public.start_page_title') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <style>
        .landing { position:relative; display:flex; min-height:100vh; flex-direction:column; overflow:hidden; color:var(--text); background:var(--bg); font-family:'Cormorant Garamond',serif; }
        .landing::before { position:absolute; inset:0; pointer-events:none; content:""; background:radial-gradient(ellipse at 50% 44%,rgba(202,164,106,.105),transparent 45%),linear-gradient(110deg,rgba(255,255,255,.018),transparent 34%,rgba(0,0,0,.10)); }
        .landing-header,.landing-main,.landing-footer { position:relative; z-index:1; width:min(1240px,calc(100% - 64px)); margin-inline:auto; }
        .landing-header { display:flex; min-height:78px; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border-soft); }
        .landing-brand { display:inline-flex; align-items:center; text-decoration:none; }
        .landing-brand img { display:block; width:auto; height:42px!important; max-width:min(48vw,190px); object-fit:contain; }
        .landing-header .theme-toggle { color:var(--heading); background:var(--surface); }
        .landing-main { display:flex; flex:1; flex-direction:column; align-items:center; justify-content:center; padding:68px 18px 72px; text-align:center; }
        .landing-kicker { display:flex; align-items:center; justify-content:center; gap:14px; margin:0 0 28px; color:var(--heading); font:600 10px/1.5 Cinzel,serif; letter-spacing:.28em; text-transform:uppercase; }
        .landing-kicker::before,.landing-kicker::after { width:46px; height:1px; content:""; background:var(--ornament); opacity:.8; }
        .landing-logo { display:block; width:min(440px,78vw); height:auto; max-height:190px; margin:0 auto 24px; object-fit:contain; filter:drop-shadow(0 8px 18px rgba(0,0,0,.28)); }
        .landing-rule { display:flex; width:min(340px,68vw); align-items:center; justify-content:center; gap:12px; margin:0 auto 22px; color:var(--ornament); }
        .landing-rule::before,.landing-rule::after { height:1px; flex:1; content:""; background:linear-gradient(90deg,transparent,var(--border)); }
        .landing-rule::after { transform:rotate(180deg); }
        .landing-rule span { font-size:15px; line-height:1; }
        .landing-title { max-width:760px; margin:0; color:var(--text); font:500 clamp(34px,5vw,58px)/1.1 'Cormorant Garamond',serif; }
        .landing-description { max-width:530px; margin:14px 0 30px; color:var(--text-soft); font:400 clamp(18px,2vw,21px)/1.45 'Cormorant Garamond',serif; }
        .landing-enter { display:inline-flex; min-width:208px; min-height:48px; align-items:center; justify-content:center; gap:18px; padding:0 20px; border:1px solid var(--accent-solid); border-radius:2px; background:var(--accent-solid); color:var(--on-accent)!important; box-shadow:3px 4px 0 var(--shadow); font:600 11px/1 Cinzel,serif; letter-spacing:.18em; text-decoration:none; text-transform:uppercase; transition:transform .18s ease,box-shadow .18s ease; }
        .landing-enter::after { content:"→"; font:20px/1 'Cormorant Garamond',serif; }
        .landing-enter:hover { transform:translate(2px,2px); box-shadow:1px 2px 0 var(--shadow); }
        .landing-footer { display:flex; min-height:54px; align-items:center; justify-content:center; gap:14px; border-top:1px solid var(--border-soft); color:var(--text-soft); font:10px/1.4 Cinzel,serif; letter-spacing:.2em; text-align:center; text-transform:uppercase; }
        .landing-footer span+span::before { margin-right:14px; color:var(--heading); content:"✦"; }
        @media(max-width:600px) {
            .landing-header,.landing-main,.landing-footer { width:calc(100% - 36px); }
            .landing-header { min-height:68px; }
            .landing-main { padding:56px 0; }
            .landing-kicker { gap:9px; font-size:9px; letter-spacing:.2em; }
            .landing-kicker::before,.landing-kicker::after { width:24px; }
            .landing-logo { width:min(360px,82vw); margin-bottom:18px; }
            .landing-title { font-size:clamp(32px,9vw,46px); }
            .landing-footer { flex-wrap:wrap; gap:8px 12px; padding:14px 0; font-size:9px; }
        }
        @media(prefers-reduced-motion:reduce) { .landing-enter { transition:none; } }
    </style>
</head>
<body class="landing">
    <div class="landing-header">
        <a class="landing-brand" href="{{ url('/') }}" aria-label="LoreRoom, accueil">@include('partials.brand-logo', ['size' => 64])</a>
        @include('partials.theme-toggle')
    </div>
    <main class="landing-main">
        <p class="landing-kicker">Le musée de vos histoires</p>
        <img class="landing-logo" src="{{ asset('loreroom.png') }}" alt="LoreRoom">
        <div class="landing-rule" aria-hidden="true"><span>✦</span></div>
        <h1 class="landing-title">Chaque monde garde une histoire.</h1>
        <p class="landing-description">Retrouvez vos personnages, leurs récits et les souvenirs qui donnent vie à vos univers.</p>
        <a class="landing-enter" href="{{ route('login') }}">{{ __('app.public.enter') }}</a>
    </main>
    <footer class="landing-footer"><span>Personnages</span><span>Récits</span><span>Univers</span></footer>
    <script>
        (function () {
            var root = document.documentElement;
            var toggles = document.querySelectorAll('[data-theme-toggle]');
            var meta = document.querySelector('[data-theme-color]');
            function applyTheme(theme) {
                root.dataset.theme = theme;
                toggles.forEach(function (button) {
                    button.setAttribute('aria-checked', String(theme === 'light'));
                    var label = button.querySelector('[data-theme-label]');
                    if (label) label.textContent = theme === 'light' ? 'Nuit' : 'Jour';
                });
                if (meta) meta.content = theme === 'light' ? '#efe2c9' : '#111723';
            }
            applyTheme(root.dataset.theme === 'auto' ? (matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark') : root.dataset.theme);
            toggles.forEach(function (button) {
                button.addEventListener('click', function () {
                    var theme = root.dataset.theme === 'light' ? 'dark' : 'light';
                    applyTheme(theme);
                    document.cookie = 'theme=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
                });
            });
        })();
    </script>
</body>
</html>