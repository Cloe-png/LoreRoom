<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Gestion LoreRoom')</title>
        <style>
            :root {
                --ink: #2d2318;
                --paper: #f3e7cc;
                --paper-2: #ecd8b2;
                --line-blue: rgba(73, 120, 177, 0.26);
                --line-red: rgba(188, 72, 72, 0.4);
                --museum-1: #101722;
                --museum-2: #1a2432;
                --gold: #e4bf7f;
                --gold-line: rgba(228, 191, 127, 0.34);
                --nav-1: #f2c879;
                --nav-2: #8dc7b0;
                --nav-3: #93add9;
                --nav-4: #e3a0a0;
                --nav-5: #c9b1e4;
            }
            * { box-sizing: border-box; }
            body {
                margin:0;
                color:#f1e7d3;
                font-family:"Garamond","Georgia",serif;
                background:
                    radial-gradient(900px 460px at 50% -6%, rgba(255, 223, 167, 0.2), transparent 66%),
                    linear-gradient(180deg,var(--museum-2),var(--museum-1));
                position: relative;
                min-height: 100vh;
            }
            body::before {
                content: "";
                position: fixed;
                inset: 0;
                background-image: url('{{ asset('museum_background.jpg') }}');
                background-size: cover;
                background-position: center;
                filter: blur(4px) saturate(0.85);
                opacity: 0.18;
                transform: scale(1.04);
                pointer-events: none;
                z-index: 0;
            }
            body::after {
                content: "";
                position: fixed;
                inset: 0;
                pointer-events: none;
                background:
                    radial-gradient(1000px 360px at 50% 0%, rgba(255, 242, 206, 0.18), transparent 70%),
                    repeating-linear-gradient(0deg, rgba(255,255,255,0.018) 0 1px, transparent 1px 5px);
                z-index: 0;
            }
            .wrap {
                position: relative;
                z-index: 1;
                width:min(1360px,97vw);
                margin:16px auto;
                border:1px solid var(--gold-line);
                border-radius:16px;
                background:rgba(16, 22, 33, 0.76);
                box-shadow:0 26px 84px rgba(0,0,0,.52);
                overflow:hidden;
            }
            .chrome {
                display:grid;
                grid-template-columns: 290px 1fr;
                min-height: calc(100vh - 32px);
                transition:grid-template-columns 180ms ease;
            }
            .chrome.is-collapsed {
                grid-template-columns: 0 1fr;
            }
            .sidebar {
                border-right:1px solid var(--gold-line);
                background:
                    linear-gradient(180deg, rgba(242, 227, 197, .96), rgba(224, 205, 171, .97)),
                    repeating-linear-gradient(0deg, transparent 0 27px, rgba(0, 0, 0, .32) 27px 28px),
                    linear-gradient(90deg, rgba(0, 0, 0, .3) 0 2px, transparent 2px 100%);
                padding:16px 10px 18px;
                display:flex;
                flex-direction:column;
                overflow:hidden;
                transition:opacity 140ms ease;
            }
            .chrome.is-collapsed .sidebar {
                opacity:0;
                pointer-events:none;
            }
            .brand {
                border:1px solid var(--gold-line);
                border-radius:12px;
                padding:12px 12px 10px;
                margin-bottom:10px;
                background:rgba(255,255,255,.36);
                box-shadow: inset 0 0 0 1px rgba(255, 236, 202, 0.2);
            }
            .brand h1 {
                margin:0;
                font-family:"Cinzel","Times New Roman",serif;
                font-size:1.04rem;
                letter-spacing:.06em;
                color:#4e381d;
            }
            .brand p {
                margin:5px 0 0;
                font-size:.78rem;
                color:#6d5537;
                text-transform:uppercase;
                letter-spacing:.08em;
            }
            .nav-group { margin-top:9px; }
            .nav-title {
                margin:10px 8px 7px;
                color:#6f5639;
                text-transform:uppercase;
                letter-spacing:.1em;
                font-size:.74rem;
            }
            .nav-link {
                display:flex;
                flex-direction:column;
                gap:2px;
                text-decoration:none;
                color:#191410;
                border:0;
                border-radius:0;
                padding:5px 8px 6px;
                font-size:.9rem;
                font-family:"Segoe Print","Comic Sans MS",cursive;
                margin-bottom:4px;
                background: transparent;
                box-shadow: none;
                transition: transform 140ms ease, filter 140ms ease;
            }
            .nav-name {
                font-weight: 600;
                letter-spacing: .01em;
            }
            .nav-link:hover {
                transform: translateX(2px);
                text-decoration: underline;
                text-decoration-thickness: 1px;
                text-underline-offset: 3px;
            }
            .nav-link.active {
                transform: translateX(2px);
            }
            .nav-link.active .nav-name {
                text-decoration: underline;
                text-decoration-thickness: 2px;
                text-underline-offset: 4px;
            }
            .main { display:flex; flex-direction:column; min-width:0; }
            header {
                padding:14px 18px;
                border-bottom:1px solid var(--gold-line);
                display:flex; justify-content:space-between; align-items:center; gap:12px;
                background:rgba(255,255,255,.03);
            }
            .header-left {
                display:flex;
                align-items:center;
                gap:10px;
            }
            .sidebar-toggle {
                width:32px;
                height:32px;
                border-radius:8px;
                border:1px solid rgba(228,191,127,.45);
                background:rgba(255,255,255,.06);
                color:#f6e8cb;
                cursor:pointer;
                font-size:1rem;
                line-height:1;
                transition:background 120ms ease, transform 180ms ease;
            }
            .sidebar-toggle:hover { background:rgba(255,255,255,.12); }
            .chrome.is-collapsed .sidebar-toggle { transform:rotate(180deg); }
            .title {
                margin:0;
                font-family:"Segoe Print","Comic Sans MS",cursive;
                letter-spacing:.02em;
                font-size:clamp(1.2rem,2.1vw,1.65rem);
                color:#f6e8cb;
            }
            .topline {
                color:#dbc9a8;
                font-size:.76rem;
                letter-spacing:.1em;
                text-transform:uppercase;
            }
            .sidebar-bottom {
                margin-top:auto;
                padding-top:12px;
                border-top:1px dashed rgba(111, 86, 57, .35);
            }
            .back-portals-link {
                display:block;
                text-decoration:none;
                color:#191410;
                font-size:.9rem;
                font-family:"Segoe Print","Comic Sans MS",cursive;
                padding:5px 8px 6px;
            }
            .back-portals-link:hover {
                text-decoration:underline;
                text-decoration-thickness:1px;
                text-underline-offset:3px;
            }
            .content {
                position: relative;
                padding:18px 20px 20px 64px;
                color:var(--ink);
                background:
                    linear-gradient(180deg, rgba(249, 238, 216, .98), rgba(236, 220, 189, .97)),
                    repeating-linear-gradient(0deg, transparent 0 27px, var(--line-blue) 27px 28px),
                    repeating-linear-gradient(90deg, rgba(122, 89, 55, .05) 0 1px, transparent 1px 8px);
            }
            .content::before {
                content: "";
                position: absolute;
                left: 42px;
                top: 0;
                bottom: 0;
                width: 2px;
                background: var(--line-red);
            }
            .content::after {
                content: "";
                position: absolute;
                left: 12px;
                top: 18px;
                bottom: 18px;
                width: 18px;
                background:
                    radial-gradient(circle at 50% 4px, rgba(122, 97, 66, .55) 0 4px, transparent 4px 22px),
                    radial-gradient(circle at 50% 26px, rgba(122, 97, 66, .55) 0 4px, transparent 4px 22px);
                background-size: 18px 44px;
                opacity: .75;
            }
            .content * {
                scrollbar-width: thin;
                scrollbar-color: #7f6238 rgba(80, 58, 32, .18);
            }
            .content *::-webkit-scrollbar {
                width: 12px;
                height: 12px;
            }
            .content *::-webkit-scrollbar-track {
                border-radius: 10px;
                background: linear-gradient(180deg, rgba(86, 65, 40, .2), rgba(60, 44, 24, .28));
            }
            .content *::-webkit-scrollbar-thumb {
                border-radius: 10px;
                border: 2px solid rgba(56, 38, 18, .35);
                background: linear-gradient(180deg, #d4b07a, #8e6a3b);
            }
            .content *::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(180deg, #e2c08c, #a37942);
            }
            .grid-4 { display:grid; gap:12px; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); }
            .card,.panel {
                border:1px solid rgba(114, 84, 49, .35);
                border-radius:10px;
                background: rgba(255,255,255,.22);
                box-shadow: 0 8px 18px rgba(70, 45, 19, .12);
                color:var(--ink);
            }
            .card { padding:12px; position: relative; }
            .card::before {
                content:"";
                position:absolute;
                right:10px; top:-9px;
                width:42px; height:14px;
                background: rgba(208, 179, 131, .45);
                border-radius:2px;
                transform: rotate(3deg);
            }
            .panel { margin-top:14px; padding:14px; }
            .metric {
                font-size:2rem;
                color:#6f4c26;
                margin:4px 0 2px;
                font-family:"Cinzel","Times New Roman",serif;
            }
            table { width:100%; border-collapse:collapse; }
            th,td { border-bottom:1px dashed rgba(114,84,49,.32); padding:8px; text-align:left; vertical-align:top; }
            th {
                color:#674620;
                font-family:"Cinzel","Times New Roman",serif;
                font-size:.82rem; letter-spacing:.05em; text-transform:uppercase;
            }
            .btn {
                display:inline-block; text-decoration:none;
                border:1px solid rgba(89, 65, 37, .38);
                color:#2f2418;
                border-radius:7px;
                padding:7px 10px;
                background:linear-gradient(180deg, #f9d48f, #e9b461);
                font-size:.9rem;
                font-family:"Segoe Print","Comic Sans MS",cursive;
                font-weight:600;
            }
            .btn.secondary { background:linear-gradient(180deg,#bdd0ed,#8ea8d0); color:#1f2a3d; }
            .btn.danger { background:linear-gradient(180deg,#ecb8b8,#d48989); color:#4a1f1f; }
            .stack { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
            .field { margin-bottom:12px; }
            .field label {
                display:block; margin-bottom:6px;
                color:#5b4328; font-size:.92rem;
                font-family:"Segoe Print","Comic Sans MS",cursive;
            }
            .field input,.field select,.field textarea {
                width:100%;
                border:1px solid rgba(101,74,42,.28);
                border-radius:8px;
                background:rgba(255,255,255,.55);
                color:#2d2318;
                padding:9px 10px;
                font-family:inherit;
            }
            .field textarea { min-height:120px; resize:vertical; }
            .flash {
                margin-bottom:12px; padding:10px 12px; border-radius:8px;
                border:1px solid rgba(83,173,120,.45);
                background:rgba(180, 236, 200, .55);
                color:#204a32;
            }
            .errors {
                margin-bottom:12px; padding:10px 12px; border-radius:8px;
                border:1px solid rgba(169,50,50,.45);
                background:rgba(246, 206, 206, .62);
                color:#5c2121;
            }
            .muted { color:#70573a; font-size:.9rem; }
            form.inline { display:inline; }
            @media (max-width: 980px) {
                .chrome { grid-template-columns: 1fr; }
                .sidebar { border-right:none; border-bottom:1px solid var(--gold-line); }
                .content { padding-left: 18px; }
                .content::before, .content::after { display:none; }
            }
        </style>
    </head>
    <body>
        <div class="wrap">
            <div class="chrome">
                <aside class="sidebar">
                    <div class="brand">
                        <h1>LoreRoom</h1>
                    </div>
                    <div class="nav-group">
                        <a class="nav-link @if(request()->routeIs('manage.index')) active @endif" href="{{ route('manage.index') }}">
                            <span class="nav-name">Accueil</span>
                        </a>
                    </div>
                    <div class="nav-group">
                        <a class="nav-link @if(request()->routeIs('manage.worlds.*')) active @endif" href="{{ route('manage.worlds.index') }}">
                            <span class="nav-name">Mondes</span>
                        </a>
                        <a class="nav-link @if(request()->routeIs('manage.characters.*')) active @endif" href="{{ route('manage.characters.index') }}">
                            <span class="nav-name">Personnages</span>
                        </a>
                        <a class="nav-link @if(request()->routeIs('manage.places.*')) active @endif" href="{{ route('manage.places.index') }}">
                            <span class="nav-name">Lieux</span>
                        </a>
                        <a class="nav-link @if(request()->routeIs('manage.chronicles.*')) active @endif" href="{{ route('manage.chronicles.index') }}">
                            <span class="nav-name">Frise chronologique</span>
                        </a>
                        <a class="nav-link @if(request()->routeIs('manage.maps.*')) active @endif" href="{{ route('manage.maps.index') }}">
                            <span class="nav-name">Cartes</span>
                        </a>
                        <a class="nav-link @if(request()->routeIs('manage.relations.*')) active @endif" href="{{ route('manage.relations.index') }}">
                            <span class="nav-name">Relations personnages</span>
                        </a>
                        <a class="nav-link @if(request()->routeIs('manage.genealogy.*')) active @endif" href="{{ route('manage.genealogy.index') }}">
                            <span class="nav-name">Arbre généalogique</span>
                        </a>
                        <a class="nav-link @if(request()->routeIs('manage.gallery.*')) active @endif" href="{{ route('manage.gallery.index') }}">
                            <span class="nav-name">Galerie</span>
                        </a>
                    </div>

                    <div class="sidebar-bottom">
                        <a class="back-portals-link" href="{{ url('/portals') }}">Retour</a>
                    </div>
                </aside>
                <section class="main">
                    <header>
                        <div class="header-left">
                            <button class="sidebar-toggle" type="button" id="sidebar-toggle" aria-label="Afficher ou cacher la navigation">&#9664;</button>
                            <h1 class="title">@yield('header', 'Gestion LoreRoom')</h1>
                        </div>
                        <div class="topline">Carnet de notes du musée</div>
                    </header>
                    <main class="content">
                        @if (session('success'))
                            <div class="flash">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="errors">
                                <strong>Validation:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @yield('content')
                    </main>
                </section>
            </div>
        </div>
        <script>
            (function () {
                const chrome = document.querySelector('.chrome');
                const toggle = document.getElementById('sidebar-toggle');
                if (!chrome || !toggle) return;

                const key = 'loreroom_manage_sidebar_collapsed';
                if (localStorage.getItem(key) === '1') {
                    chrome.classList.add('is-collapsed');
                }

                toggle.addEventListener('click', function () {
                    chrome.classList.toggle('is-collapsed');
                    localStorage.setItem(key, chrome.classList.contains('is-collapsed') ? '1' : '0');
                });
            })();
        </script>
    </body>
</html>
