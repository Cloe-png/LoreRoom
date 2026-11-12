<!DOCTYPE html>
<html data-theme="{{ auth()->check() ? (auth()->user()->theme ?? 'dark') : (in_array(request()->cookie('theme'), ['dark','light','auto'], true) ? request()->cookie('theme') : 'dark') }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', __('app.layout.default_title'))</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;500&family=Cinzel:wght@400;600;700&family=Cormorant+Garamond:wght@400;500;600;700&display=swap" rel="stylesheet">
        @unless(request()->routeIs('manage.index'))<link rel="stylesheet" href="{{ asset('css/theme.css') }}">@endunless
        @if(request()->routeIs('manage.index'))<link rel="stylesheet" href="{{ asset('css/shell.css') }}?v=20261109-2"><link rel="stylesheet" href="{{ mix('css/home.css') }}">@endif
        @unless(request()->routeIs('manage.index'))<style>
            :root {
                --paper: #efe2c9;
                --paper-ink: #3b2d1c;
                --gold: #caa46a;
                --gold-dark: #7a5931;
                --stone-1: #111723;
                --stone-2: #1c2432;
                --emerald: #2f6158;
                --danger: #a93232;
                --ink: var(--paper-ink);
                --paper-2: rgba(255,255,255,.35);
                --accent: var(--gold-dark);
                --leather: var(--stone-1);
                --line-blue: color-mix(in srgb, var(--gold) 26%, transparent);
                --line-red: color-mix(in srgb, var(--danger) 40%, transparent);
                --soft: var(--gold-dark);
                --museum-1: var(--stone-1);
                --museum-2: var(--stone-2);
                --gold-line: rgba(202,164,106,.34);
                --nav-1: var(--gold-dark);
                --nav-2: var(--emerald);
                --nav-3: var(--gold-dark);
                --nav-4: var(--danger);
                --nav-5: var(--gold-dark);
                --sidebar-width: 290px;
                --header-height: 72px;
            }
            * { box-sizing: border-box; }
            html, body { min-height: 100%; }
            body {
                margin:0;
                color:var(--paper);
                font-family:"Cormorant Garamond","EB Garamond","Garamond",Georgia,serif;
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
            body.manage-ui-locked {
                overflow: hidden;
            }
            .wrap {
                position: relative;
                width:min(1360px,97vw);
                margin:16px auto;
                border:1px solid var(--gold-line);
                border-radius:16px;
                background:rgba(16, 22, 33, 0.76);
                box-shadow:0 26px 84px rgba(0,0,0,.52);
                overflow:hidden;
            }
            /* Scholar's folio surface: compact edges, restrained relief, and aligned tabs. */
            body { background-color:var(--leather); background-image:radial-gradient(ellipse at center, transparent 42%, rgba(8,5,5,.7) 100%), repeating-linear-gradient(37deg,rgba(255,255,255,.018) 0 1px,transparent 1px 5px),linear-gradient(145deg,var(--stone-2),var(--stone-1) 72%); }
            .wrap { width:100%; min-height:100vh; margin:0; border:0; border-radius:0; background:transparent; box-shadow:none; }
            .chrome { min-height:100vh; }
            .sidebar { background-color:var(--paper); background-image:repeating-linear-gradient(0deg,transparent 0 27px,rgba(61,41,27,.09) 27px 28px),linear-gradient(90deg,rgba(61,41,27,.12),transparent 10px),url("data:image/svg+xml,%3Csvg viewBox='0 0 180 180' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.035'/%3E%3C/svg%3E"); }
            .brand-wordmark { position:relative; display:flex; align-items:baseline; gap:.16em; margin:0; color:var(--paper-ink); font:700 1.45rem/1 "Cinzel",serif; letter-spacing:.12em; white-space:nowrap; }
            .brand-wordmark i { align-self:flex-start; margin-left:2px; color:var(--gold-dark); font-size:.58rem; font-style:normal; }
            .nav-link,.back-portals-link { width:100%; min-height:32px; flex-direction:row; align-items:center; text-align:left; justify-content:flex-start; padding:6px 10px 6px 14px; margin:0 0 3px; border-radius:2px; font-family:"Cormorant Garamond","EB Garamond","Garamond",Georgia,serif; font-size:1rem; }
            .nav-link.active { color:var(--accent); background:linear-gradient(90deg,var(--accent) 0 4px,rgba(107,30,42,.1) 4px); transform:none; }
            .nav-link.active .nav-name { text-decoration:none; font-weight:700; }
            .nav-accordion-body { padding-left:10px; }
            .sidebar-bottom .nav-link, .sidebar-bottom form { margin:0; }
            .sidebar-bottom form button { font:inherit; }
            .sidebar-bottom { border-top:1px dashed rgba(111,86,57,.5); }
            header { background:var(--stone-1); border-bottom:1px solid rgba(228,191,127,.4); }
            .title { font-family:"Cinzel", "Cormorant Garamond",serif; }
            .sidebar-toggle { width:28px; height:28px; border-radius:2px; border-color:rgba(228,191,127,.38); background:transparent; }
            .topline { display:inline-block; padding:6px 9px; border:1px solid rgba(228,191,127,.58); color:var(--paper); border-radius:2px; }
            .topline a { color:var(--paper); }
            .content { flex:1; background-color:var(--paper); background-image:repeating-linear-gradient(0deg,transparent 0 27px,rgba(73,120,177,.12) 27px 28px),url("data:image/svg+xml,%3Csvg viewBox='0 0 180 180' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E"); }
            .content::before { left:42px; width:3px; background:rgba(147,39,52,.62); }
            .content::after { background:radial-gradient(circle at 50% 4px,var(--gold-dark) 0 4px,var(--gold-dark) 4.5px 5px,transparent 5.5px 22px),radial-gradient(circle at 50% 26px,var(--gold-dark) 0 4px,var(--gold-dark) 4.5px 5px,transparent 5.5px 22px); opacity:.9; }
            .card,.panel { border-radius:4px; box-shadow:2px 3px 0 rgba(60,40,20,.2); }
            .chrome {
                display:grid;
                grid-template-columns: var(--sidebar-width) 1fr;
                min-height: calc(100vh - 32px);
                transition:grid-template-columns 180ms ease;
            }
            .chrome.is-collapsed {
                grid-template-columns: 0 1fr;
            }
            .drawer-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(8, 12, 18, .54);
                backdrop-filter: blur(3px);
                opacity: 0;
                pointer-events: none;
                transition: opacity 180ms ease;
                z-index: 8;
            }
            body.manage-ui-drawer-open .drawer-backdrop {
                opacity: 1;
                pointer-events: auto;
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
            .sidebar-mobile-head {
                display: none;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
                margin-bottom: 12px;
                padding: 0 2px;
            }
            .sidebar-mobile-label {
                color: var(--gold-dark);
                font-size: .78rem;
                text-transform: uppercase;
                letter-spacing: .1em;
            }
            .sidebar-close {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                border: 1px solid rgba(112, 82, 47, .24);
                background: rgba(255,255,255,.68);
                color: var(--gold-dark);
                font-size: 1rem;
            }
            .chrome.is-collapsed .sidebar {
                opacity:0;
                pointer-events:none;
            }
            .brand {
                padding:8px 6px 10px;
                margin-bottom:10px;
                background:transparent;
                border:0;
                box-shadow:none;
            }
            .brand h1 {
                margin:0;
            }
            .brand-logo {
                display: block;
                height: 80px;
                width: auto;
                max-width: 220px;
                border-radius: 0;
                object-fit: contain;
                box-shadow: none;
            }
            .brand p {
                margin:5px 0 0;
                font-size:.78rem;
                color:var(--gold-dark);
                text-transform:uppercase;
                letter-spacing:.08em;
            }
            .nav-group { margin-top:9px; }
            .nav-title {
                margin:10px 8px 7px;
                color:var(--gold-dark);
                text-transform:uppercase;
                letter-spacing:.1em;
                font-size:.74rem;
            }
            .nav-link {
                display:flex;
                flex-direction:column;
                gap:2px;
                text-decoration:none;
                color:var(--stone-1);
                border:0;
                border-radius:0;
                padding:5px 8px 6px;
                font-size:.9rem;
                font-family:"Segoe Print","Comic Sans MS",cursive;
                margin-bottom:4px;
                background: transparent;
                box-shadow: none;
                transition: transform 140ms ease, filter 140ms ease;
                align-items: flex-start;
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
            .nav-name-link {
                color: inherit;
                text-decoration: none;
            }
            .nav-name-link:hover { text-decoration: underline; }
            .nav-item-actions {
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
            }
            .nav-accordion {
                border: 0;
                border-radius: 0;
                margin: 0 0 10px;
                background: transparent;
                overflow: visible;
                width: 100%;
            }
            .nav-accordion > summary {
                cursor: pointer;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                list-style: none;
                background: transparent;
                text-align: left;
            }
            .nav-accordion > summary.nav-link {
                flex-direction: row;
            }
            .nav-accordion > summary .nav-name {
                font-weight: 600;
                letter-spacing: .01em;
            }
            .nav-accordion[open] > summary .nav-name {
                text-decoration: underline;
                text-decoration-thickness: 2px;
                text-underline-offset: 4px;
            }
            .nav-accordion > summary::after {
                content: "\25B8";
                font-size: .9rem;
                color: var(--gold-dark);
                transition: transform 140ms ease;
                margin-left: 8px;
            }
            .nav-accordion[open] > summary::after {
                transform: rotate(90deg);
            }
            .nav-accordion > summary::-webkit-details-marker { display: none; }
            .nav-accordion-body { padding: 6px 0 0 10px; }
            .nav-faction-item {
                display: grid;
                gap: 6px;
                padding: 6px 0;
                border-bottom: 1px dashed rgba(111, 86, 57, .25);
            }
            .nav-faction-item:last-child { border-bottom: 0; }
            .nav-faction-name {
                color: var(--paper-ink);
                text-decoration: none;
                font-size: .9rem;
            }
            .nav-faction-name:hover { text-decoration: underline; }
            .nav-faction-actions {
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
            }
            .btn-mini {
                border: 1px solid rgba(89, 65, 37, .38);
                background: rgba(255,255,255,.7);
                color: var(--paper-ink);
                border-radius: 6px;
                padding: 4px 8px;
                font-size: .78rem;
                font-family: "Segoe Print","Comic Sans MS",cursive;
                text-decoration: none;
                display: inline-block;
            }
            .btn-mini.danger { background: rgba(236, 184, 184, .85); color:var(--danger); }
            .btn-mini.secondary { background: rgba(189, 208, 237, .85); color:var(--stone-2); }
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
                min-width: 0;
            }
            .header-actions {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
                justify-content: flex-end;
            }
            .sidebar-toggle,
            .header-toggle {
                width:32px;
                height:32px;
                border-radius:8px;
                border:1px solid rgba(228,191,127,.45);
                background:rgba(255,255,255,.06);
                color:var(--paper);
                cursor:pointer;
                font-size:1rem;
                line-height:1;
                transition:background 120ms ease, transform 180ms ease;
            }
            .sidebar-toggle:hover,
            .header-toggle:hover { background:rgba(255,255,255,.12); }
            .chrome.is-collapsed .sidebar-toggle { transform:rotate(180deg); }
            .mobile-nav-toggle {
                display: none;
            }
            .desktop-nav-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            .title {
                margin:0;
                font-family:"Segoe Print","Comic Sans MS",cursive;
                letter-spacing:.02em;
                font-size:clamp(1.2rem,2.1vw,1.65rem);
                color:var(--paper);
                min-width: 0;
            }
            .topline {
                color:var(--paper);
                font-size:.76rem;
                letter-spacing:.1em;
                text-transform:uppercase;
            }
            .topline a {
                color:var(--paper);
            }
            .header-context {
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                gap: 8px;
                min-width: 0;
            }
            .header-glance {
                display: flex;
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 8px;
            }
            .glance-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                min-height: 32px;
                padding: 6px 12px;
                border-radius: 999px;
                border: 1px solid rgba(228,191,127,.22);
                background: rgba(255,255,255,.07);
                color: var(--paper);
                text-decoration: none;
                font-size: .78rem;
                letter-spacing: .05em;
                text-transform: uppercase;
                white-space: nowrap;
            }
            .glance-chip strong {
                color: var(--paper);
                font-weight: 700;
            }
            .sidebar-bottom {
                margin-top:auto;
                padding-top:12px;
                border-top:1px dashed rgba(111, 86, 57, .35);
            }
            .back-portals-link {
                display:block;
                text-decoration:none;
                color:var(--stone-1);
                font-size:.9rem;
                font-family:"Segoe Print","Comic Sans MS",cursive;
                padding:5px 8px 6px;
            }
            button.back-portals-link {
                width: 100%;
                text-align: left;
                border: 0;
                background: transparent;
                cursor: pointer;
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
                scrollbar-color: var(--gold-dark) rgba(80, 58, 32, .18);
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
                background: linear-gradient(180deg, var(--gold), var(--gold-dark));
            }
            .content *::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(180deg, var(--gold), var(--gold-dark));
            }
            /* Final editorial details for the folio dashboard. */
            .nav-link,.back-portals-link { box-sizing:border-box; width:100%; min-height:0; padding:15px 28px; margin:0; border:0; border-radius:0; color:var(--paper-ink); font:400 13px/1.25 "Cinzel",serif; letter-spacing:.16em; text-align:left; text-transform:uppercase; transition:padding-left 180ms ease,color 180ms ease,background 180ms ease; }
            .nav-link { display:flex; flex-direction:row; align-items:center; justify-content:flex-start; }
            .nav-link:hover,.back-portals-link:hover { padding-left:34px; color:var(--accent); text-decoration:none; background:transparent; box-shadow:none; transform:none; }
            .nav-link.active { position:relative; padding-left:28px; color:var(--accent); font-weight:700; background:linear-gradient(90deg,rgba(107,30,42,.1),transparent); box-shadow:none; }
            .nav-link.active::before { content:""; position:absolute; left:0; top:50%; width:14px; height:44px; transform:translateY(-50%); background:var(--accent); clip-path:polygon(0 0,100% 0,100% 100%,50% 78%,0 100%); }
            .nav-link.active .nav-name { font-weight:700; text-decoration:none; }
            .nav-accordion-body { padding:0; }
            .sidebar-bottom { padding-top:12px; }
            .sidebar-bottom form button.back-portals-link { font:400 13px/1.25 "Cinzel",serif; letter-spacing:.16em; text-transform:uppercase; }
            header { background:linear-gradient(110deg,var(--stone-1),var(--stone-1)); border-bottom:2px solid var(--gold); }
            .sidebar-toggle { width:38px; height:38px; border:1px solid var(--gold); border-radius:2px; background:transparent; }
            .content::before { width:2px; background:var(--danger); }
            .content::after { background:radial-gradient(circle at 50% 7px,var(--stone-1) 0 5px,var(--gold) 5.5px 7px,transparent 7.5px); background-size:22px 46px; background-repeat:repeat-y; opacity:1; }
            body::before,body::after,.content { background-attachment:scroll!important; }
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
                color:var(--gold-dark);
                margin:4px 0 2px;
                font-family:"Cinzel","Times New Roman",serif;
            }
            table { width:100%; border-collapse:collapse; }
            th,td { border-bottom:1px dashed rgba(114,84,49,.32); padding:8px; text-align:left; vertical-align:top; }
            th {
                color:var(--gold-dark);
                font-family:"Cinzel","Times New Roman",serif;
                font-size:.82rem; letter-spacing:.05em; text-transform:uppercase;
            }
            .btn {
                display:inline-block; text-decoration:none;
                border:1px solid rgba(89, 65, 37, .38);
                color:var(--paper-ink);
                border-radius:7px;
                padding:7px 10px;
                background:linear-gradient(180deg, var(--gold), var(--gold));
                font-size:.9rem;
                font-family:"Segoe Print","Comic Sans MS",cursive;
                font-weight:600;
            }
            .btn.secondary { background:linear-gradient(180deg,var(--paper),var(--gold-dark)); color:var(--stone-2); }
            .btn.danger { background:linear-gradient(180deg,var(--danger),var(--danger)); color:var(--danger); }
            .stack { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
            .field { margin-bottom:12px; }
            .field label {
                display:block; margin-bottom:6px;
                color:var(--gold-dark); font-size:.92rem;
                font-family:"Segoe Print","Comic Sans MS",cursive;
            }
            .field input,.field select,.field textarea {
                width:100%;
                border:1px solid rgba(101,74,42,.28);
                border-radius:8px;
                background:rgba(255,255,255,.55);
                color:var(--paper-ink);
                padding:9px 10px;
                font-family:inherit;
            }
            .field input[type="color"] {
                height:44px;
                padding:4px;
                cursor:pointer;
            }
            .field textarea { min-height:120px; resize:vertical; }
            .flash {
                margin-bottom:12px; padding:10px 12px; border-radius:8px;
                border:1px solid rgba(83,173,120,.45);
                background:rgba(180, 236, 200, .55);
                color:var(--emerald);
            }
            .errors {
                margin-bottom:12px; padding:10px 12px; border-radius:8px;
                border:1px solid rgba(169,50,50,.45);
                background:rgba(246, 206, 206, .62);
                color:var(--danger);
            }
            .muted { color:var(--gold-dark); font-size:.9rem; }
            form.inline { display:inline; }
            .touch-surface {
                overflow: auto;
                -webkit-overflow-scrolling: touch;
                touch-action: pan-x pan-y;
            }
            @media (max-width: 980px) {
                .chrome { grid-template-columns: 1fr !important; }
                .drawer-backdrop {
                    z-index: 18;
                }
                .sidebar {
                    position: fixed;
                    left: 0;
                    top: 0;
                    bottom: 0;
                    width: min(82vw, 332px);
                    border-right:1px solid var(--gold-line);
                    border-bottom:0;
                    transform: translateX(-102%);
                    opacity: 1;
                    pointer-events: auto;
                    transition: transform 180ms ease;
                    z-index: 22;
                    box-shadow: 0 24px 64px rgba(0,0,0,.38);
                    overflow-y: auto;
                }
                body.manage-ui-drawer-open .sidebar {
                    transform: translateX(0);
                }
                .content { padding-left: 18px; }
                .content::before, .content::after { display:none; }
                .wrap {
                    width: 100vw;
                    margin: 0;
                    border-radius: 0;
                }
                .sidebar-mobile-head {
                    display: flex;
                }
                .sidebar {
                    padding: 14px 12px 18px;
                }
                .brand {
                    margin-bottom: 10px;
                    padding: 14px 12px;
                }
                .brand-logo {
                    max-width: min(100%, 220px);
                }
                .nav-sections {
                    display: grid;
                    gap: 10px;
                    overflow: visible;
                    padding-bottom: 0;
                    scroll-snap-type: none;
                }
                .nav-section {
                    min-width: 0;
                    border-radius: 10px;
                }
                .nav-section > summary {
                    padding: 10px 10px;
                    font-size: .8rem;
                    letter-spacing: .05em;
                }
                .nav-section-body {
                    padding: 0 8px 10px;
                }
                .nav-group {
                    margin-top: 4px;
                }
                .nav-link {
                    padding: 7px 8px 8px;
                    margin-bottom: 3px;
                    font-size: .84rem;
                    line-height: 1.24;
                }
                .sidebar-bottom {
                    margin-top: 8px;
                }
                header {
                    flex-direction: column;
                    align-items: flex-start;
                    position: sticky;
                    top: 0;
                    z-index: 6;
                    backdrop-filter: blur(8px);
                }
                .header-left {
                    width: 100%;
                    justify-content: flex-start;
                    gap: 8px;
                }
                .header-context {
                    width: 100%;
                    align-items: flex-start;
                }
                .header-actions,
                .header-glance {
                    width: 100%;
                    justify-content: flex-start;
                }
                .mobile-nav-toggle {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                }
                .desktop-nav-toggle {
                    display: none;
                }
            }
            @media (max-width: 640px) {
                .brand-logo {
                    max-width: min(100%, 220px);
                }
                .nav-section {
                    min-width: 0;
                }
                .nav-section > summary {
                    padding: 9px 9px;
                    font-size: .76rem;
                }
                .nav-link {
                    font-size: .79rem;
                }
                .content {
                    padding: 14px 14px 18px;
                }
                .glance-chip {
                    font-size: .74rem;
                }
            }
            @media (min-width: 981px) {
                .nav-section {
                    border: 0;
                    border-radius: 0;
                    background: transparent;
                }
                .nav-section > summary {
                    padding: 0 8px 4px;
                }
                .nav-section > summary::after {
                    display: none;
                }
                .nav-section-body {
                    padding: 0;
                }
            }
        </style>@endunless
<meta name="theme-color" content="{{ (auth()->check() ? auth()->user()->theme : request()->cookie('theme')) === 'light' ? '#efe2c9' : '#111723' }}" data-theme-color>
<script>if(document.documentElement.dataset.theme==='auto'){document.documentElement.dataset.theme=matchMedia('(prefers-color-scheme: light)').matches?'light':'dark';var tc=document.querySelector('[data-theme-color]');if(tc)tc.content=document.documentElement.dataset.theme==='light'?'#efe2c9':'#111723';}</script>
        <link rel="stylesheet" href="{{ asset('css/tour.css') }}">
</head>
    <body class="@if(request()->routeIs('manage.index'))manage-dashboard @endif">
        @if(request()->routeIs('manage.index'))
        <div class="app">
            <aside class="side">
                <a class="logo" href="{{ route('manage.index') }}" aria-label="LoreRoom — accueil">LOREROOM</a><div class="orn" aria-hidden="true"><i></i><span>&#10022;</span><i></i></div>
                <nav class="nav">
                    <a class="on" href="{{ route('manage.index') }}" data-tour="home">Accueil</a>
                    <details @if(request()->routeIs('manage.worlds.*','manage.characters.*','manage.places.*','manage.chapters.*','manage.lore.*','manage.jobs.*','manage.factions.*','manage.species.*')) open @endif>
                        <summary class="par">Gestion <small>&#9662;</small></summary>
                        <a class="sub" href="{{ route('manage.worlds.index') }}" data-tour="worlds">Mondes</a><a class="sub" href="{{ route('manage.characters.index') }}" data-tour="characters">Personnages</a><a class="sub" href="{{ route('manage.places.index') }}" data-tour="places">Lieux</a>
                        @if(auth()->user()->canAccessModule('chapters'))<a class="sub" href="{{ route('manage.chapters.index') }}" data-tour="chapters">Chapitres</a>@endif
                        @if(auth()->user()->canAccessModule('lore'))<a class="sub" href="{{ route('manage.species.index') }}" data-tour="species">Esp&egrave;ces</a><a class="sub" href="{{ route('manage.lore.index') }}" data-tour="lore">Lore</a>@endif
                        @if(auth()->user()->canAccessModule('jobs'))<a class="sub" href="{{ route('manage.jobs.index') }}" data-tour="jobs">M&eacute;tiers</a>@endif
                        @if(auth()->user()->canAccessModule('factions'))<a class="sub" href="{{ route('manage.factions.index') }}" data-tour="factions">Factions</a>@endif
                    </details>
                    <details @if(request()->routeIs('manage.chronicles.*','manage.relations.*','manage.genealogy.*','manage.gallery.*')) open @endif><summary class="par">Lore <small>&#9662;</small></summary><a class="sub" href="{{ route('manage.chronicles.index') }}" data-tour="chronology">Chronologie</a><a class="sub" href="{{ route('manage.relations.index') }}" data-tour="relations">Relations</a>@if(auth()->user()->canAccessModule('genealogy'))<a class="sub" href="{{ route('manage.genealogy.index') }}" data-tour="genealogy">G&eacute;n&eacute;alogie</a>@endif @if(auth()->user()->canAccessModule('gallery'))<a class="sub" href="{{ route('manage.gallery.index') }}" data-tour="gallery">Galerie</a>@endif</details>
                    <details @if(request()->routeIs('manage.account.*','manage.suggestions.*','manage.trash.*')) open @endif><summary class="par" data-tour="tools">Outils <small>&#9662;</small></summary><a class="sub" href="{{ route('manage.account.edit') }}">Param&egrave;tres</a>@if(auth()->user()->canAccessModule('suggestions'))<a class="sub" href="{{ route('manage.suggestions.index') }}">Suggestions</a>@endif @if(auth()->user()->canAccessModule('trash'))<a class="sub" href="{{ route('manage.trash.index') }}">Corbeille</a>@endif</details>
                </nav>
                <div class="sep"></div><nav class="nav">
                    @if(auth()->user()->canAccessModule('analytics'))<a href="{{ route('manage.analytics.index') }}" @if(auth()->user()->isAdminOrChef()) data-tour="analytics" @endif>Analytics</a>@endif
                    @if(auth()->user()->canAccessModule('users'))<a href="{{ route('manage.users.index') }}" @if(auth()->user()->isAdminOrChef()) data-tour="accounts" @endif>Comptes</a>@endif
                    <x-tour-restart-button />
                    <a class="q" href="#" onclick="event.preventDefault();this.nextElementSibling.submit()">D&eacute;connexion</a><form method="POST" action="{{ route('logout') }}" hidden>@csrf</form>
                </nav>
            </aside>
            <div class="main"><header class="top"><a class="back" href="{{ route('manage.index') }}" aria-label="Retour">&#9664;</a><h2>Carnet de bord</h2>
                <button class="tg" type="button" role="switch" aria-checked="false" aria-label="Th&egrave;me actuel"><svg class="moon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 14.5A8.5 8.5 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5z"/></svg><svg class="sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/></svg><span></span></button>
                <div class="ex"><span>Monde actif : {{ $activeWorld->name ?? 'Aucun' }} ? <a href="{{ route('manage.worlds.index') }}">Changer</a></span>@if(auth()->user()->isChef())<span class="chef">CHEF</span>@endif</div>
            </header><div class="page">
                @if(session('success'))<div role="status">{{ session('success') }}</div>@endif
                @if($errors->any())<div role="alert">{{ $errors->first() }}</div>@endif
                @yield('content')
            </div></div>
        </div>
        @else
        <button class="drawer-backdrop" type="button" id="drawer-backdrop" aria-hidden="true" tabindex="-1"></button>
        <div class="wrap">
            <div class="chrome">
                <aside class="sidebar">
                    <div class="sidebar-mobile-head">
                        <span class="sidebar-mobile-label">{{ __('app.layout.navigation_label') }}</span>
                        <button class="sidebar-close" type="button" id="sidebar-close" aria-label="{{ __('app.layout.close_navigation') }}">&times;</button>
                    </div>
                    @php($currentUser = auth()->user())
                    <div class="brand">
                        <h1>
                            @if(request()->routeIs('manage.analytics.*'))
                                <a class="analytics-wordmark" href="{{ route('manage.index') }}" aria-label="LoreRoom — accueil">LOREROOM</a>
                            @elseif(request()->routeIs('manage.index'))
                                <span class="brand-wordmark">LORE<span>ROOM</span></span>
                                <div class="brand-rule" aria-hidden="true"><span></span>◆<span></span></div>
                            @else
                                <a class="brand-wordmark" href="{{ route('manage.index') }}" aria-label="LoreRoom — accueil">LOREROOM</a>
                                <div class="brand-rule" aria-hidden="true"><span></span><i>✦</i><span></span></div>
                            @endif
                        </h1>
                    </div>
                    <div class="nav-group">
                    <a class="nav-link @if(request()->routeIs('manage.index')) active @endif" href="{{ route('manage.index') }}" data-tour="home">
                            <span class="nav-name">{{ __('app.nav.home') }}</span>
                        </a>
                    </div>

                    <details class="nav-accordion" @if(request()->routeIs('manage.worlds.*') || request()->routeIs('manage.characters.*') || request()->routeIs('manage.relations.*') || request()->routeIs('manage.genealogy.*') || request()->routeIs('manage.gallery.*') || request()->routeIs('manage.species.*') || request()->routeIs('manage.lore.*') || request()->routeIs('manage.jobs.*') || request()->routeIs('manage.factions.*') || request()->routeIs('manage.chapters.*') || request()->routeIs('manage.places.*')) open @endif>
                        <summary class="nav-link @if(request()->routeIs('manage.worlds.*') || request()->routeIs('manage.characters.*') || request()->routeIs('manage.relations.*') || request()->routeIs('manage.genealogy.*') || request()->routeIs('manage.gallery.*') || request()->routeIs('manage.species.*') || request()->routeIs('manage.lore.*') || request()->routeIs('manage.jobs.*') || request()->routeIs('manage.factions.*') || request()->routeIs('manage.chapters.*') || request()->routeIs('manage.places.*')) active @endif">
                            <span class="nav-name">Gestion</span>
                        </summary>
                        <div class="nav-accordion-body">
                            <a class="nav-link @if(request()->routeIs('manage.worlds.*')) active @endif" href="{{ route('manage.worlds.index') }}" data-tour="worlds">
                                <span class="nav-name">{{ __('app.nav.worlds') }}</span>
                            </a>
                            <a class="nav-link @if(request()->routeIs('manage.characters.*')) active @endif" href="{{ route('manage.characters.index') }}" data-tour="characters">
                                <span class="nav-name">{{ __('app.nav.characters') }}</span>
                            </a>
                            <a class="nav-link @if(request()->routeIs('manage.places.*')) active @endif" href="{{ route('manage.places.index') }}" data-tour="places">
                                <span class="nav-name">{{ __('app.nav.places') }}</span>
                            </a>
                            @if($currentUser && $currentUser->canAccessModule('chapters'))
                                <a class="nav-link @if(request()->routeIs('manage.chapters.*')) active @endif" href="{{ route('manage.chapters.index') }}" data-tour="chapters">
                                    <span class="nav-name">{{ __('app.nav.chapters') }}</span>
                                </a>
                            @endif
                            @if($currentUser && $currentUser->canAccessModule('lore'))
                                <a class="nav-link @if(request()->routeIs('manage.lore.*')) active @endif" href="{{ route('manage.lore.index') }}" data-tour="lore">
                                    <span class="nav-name">{{ __('app.nav.lore') }}</span>
                                </a>
                            @endif
                            @if($currentUser && $currentUser->canAccessModule('jobs'))
                                <a class="nav-link @if(request()->routeIs('manage.jobs.*')) active @endif" href="{{ route('manage.jobs.index') }}" data-tour="jobs">
                                    <span class="nav-name">{{ __('app.nav.jobs') }}</span>
                                </a>
                            @endif
                            @if($currentUser && $currentUser->canAccessModule('factions'))
                                <a class="nav-link @if(request()->routeIs('manage.factions.*')) active @endif" href="{{ route('manage.factions.index') }}" data-tour="factions">
                                    <span class="nav-name">{{ __('app.nav.factions') }}</span>
                                </a>
                            @endif
                            @if($currentUser && $currentUser->canAccessModule('lore'))
                                <a class="nav-link @if(request()->routeIs('manage.species.*')) active @endif" href="{{ route('manage.species.index') }}" data-tour="species">
                                    <span class="nav-name">Espèces</span>
                                </a>
                            @endif
                        </div>
                    </details>

                    <details class="nav-accordion" @if(request()->routeIs('manage.chronicles.*') || request()->routeIs('manage.relations.*') || request()->routeIs('manage.genealogy.*') || request()->routeIs('manage.gallery.*')) open @endif>
                        <summary class="nav-link @if(request()->routeIs('manage.chronicles.*') || request()->routeIs('manage.relations.*') || request()->routeIs('manage.genealogy.*') || request()->routeIs('manage.gallery.*')) active @endif">
                            <span class="nav-name">Lore</span>
                        </summary>
                        <div class="nav-accordion-body">
                            <a class="nav-link @if(request()->routeIs('manage.chronicles.*')) active @endif" href="{{ route('manage.chronicles.index') }}" data-tour="chronology">
                                <span class="nav-name">{{ __('app.nav.chronicles') }}</span>
                            </a>
                            <a class="nav-link @if(request()->routeIs('manage.relations.*')) active @endif" href="{{ route('manage.relations.index') }}" data-tour="relations">
                                <span class="nav-name">{{ __('app.nav.relations') }}</span>
                            </a>
                            @if($currentUser && $currentUser->canAccessModule('genealogy'))
                                <a class="nav-link @if(request()->routeIs('manage.genealogy.*')) active @endif" href="{{ route('manage.genealogy.index') }}" data-tour="genealogy">
                                    <span class="nav-name">{{ __('app.nav.genealogy') }}</span>
                                </a>
                            @endif
                            @if($currentUser && $currentUser->canAccessModule('gallery'))
                                <a class="nav-link @if(request()->routeIs('manage.gallery.*')) active @endif" href="{{ route('manage.gallery.index') }}" data-tour="gallery">
                                    <span class="nav-name">{{ __('app.nav.gallery') }}</span>
                                </a>
                            @endif
                        </div>
                    </details>

                    <details class="nav-accordion" @if(request()->routeIs('manage.suggestions.*') || request()->routeIs('manage.trash.*')) open @endif>
                        <summary class="nav-link @if(request()->routeIs('manage.suggestions.*') || request()->routeIs('manage.trash.*')) active @endif" data-tour="tools">
                            <span class="nav-name">Outils</span>
                        </summary>
                        <div class="nav-accordion-body">
                            <a class="nav-link @if(request()->routeIs('manage.account.*')) active @endif" href="{{ route('manage.account.edit') }}">
                                <span class="nav-name">{{ __('app.nav.settings') }}</span>
                            </a>
                            @if($currentUser && $currentUser->canAccessModule('suggestions'))
                                <a class="nav-link @if(request()->routeIs('manage.suggestions.*')) active @endif" href="{{ route('manage.suggestions.index') }}">
                                    <span class="nav-name">{{ __('app.nav.suggestions') }}</span>
                                </a>
                            @endif
                            @if($currentUser && $currentUser->canAccessModule('trash'))
                                <a class="nav-link @if(request()->routeIs('manage.trash.*')) active @endif" href="{{ route('manage.trash.index') }}">
                                    <span class="nav-name">{{ __('app.nav.trash') }}</span>
                                </a>
                            @endif
                        </div>
                    </details>

                    <div class="sidebar-bottom">
                        @if($currentUser && $currentUser->canAccessModule('analytics'))
                            <a class="nav-link @if(request()->routeIs('manage.analytics.*')) active @endif" href="{{ route('manage.analytics.index') }}" @if($currentUser && $currentUser->isAdminOrChef()) data-tour="analytics" @endif>
                                <span class="nav-name">{{ __('app.nav.analytics') }}</span>
                            </a>
                        @endif
                        @if($currentUser && $currentUser->canAccessModule('users'))
                            <a class="nav-link @if(request()->routeIs('manage.users.*')) active @endif" href="{{ route('manage.users.index') }}" @if($currentUser && $currentUser->isAdminOrChef()) data-tour="accounts" @endif>
                                <span class="nav-name">{{ __('app.nav.accounts') }}</span>
                            </a>
                        @endif
                        <x-tour-restart-button />
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="back-portals-link">{{ __('app.nav.logout') }}</button>
                        </form>
                        @php($memorialVisible = (bool) config('memorial.force_visible') || now(config('app.timezone'))->format('m-d') === sprintf('%02d-%02d', config('memorial.month'), config('memorial.day')))
                        @if($memorialVisible)
                            <button class="memorial-secret" type="button" aria-label="Hayate" data-destination="{{ route('manage.memorial.show') }}">
                                @if(file_exists(public_path('images/mascot/icone.png')))
                                    <img src="{{ asset('images/mascot/icone.png') }}" alt="">
                                @else
                                    <svg viewBox="0 0 40 40" aria-hidden="true" fill="currentColor"><path d="M8 17c-3-4-2-9 1-10 4-1 6 4 6 8 2-1 4-1 6-1 0-4 2-8 5-7 3 1 2 6 0 9 4 2 7 6 7 11 0 7-6 11-14 11S5 34 5 27c0-4 1-7 3-10Z"/><circle cx="14" cy="24" r="1.4" fill="var(--stone-1)"/><circle cx="25" cy="24" r="1.4" fill="var(--stone-1)"/><path d="M17 28h5l-2.5 2.5Z" fill="var(--stone-1)"/></svg>
                                @endif
                            </button>
                            <script src="{{ asset('js/memorial.js') }}" defer></script>
                        @endif
                    </div>
                </aside>
                <section class="main">
                    <header>
                        <div class="header-left">
                            <button class="sidebar-toggle mobile-nav-toggle" type="button" id="mobile-nav-toggle" aria-label="{{ __('app.layout.open_navigation') }}">&#9776;</button>
                            <button class="sidebar-toggle desktop-nav-toggle" type="button" id="sidebar-toggle" aria-label="{{ __('app.layout.toggle_navigation') }}">&#9664;</button>
                            <h1 class="title">@yield('header', __('app.layout.default_title'))</h1>
                        </div>
                        <div class="header-context">
                            @include('partials.theme-toggle')
                            <div class="topline">
                                @if(!empty($activeWorld))
                                    {{ __('app.layout.active_world', ['world' => $activeWorld->name]) }} ·
                                    <a href="{{ route('manage.worlds.index') }}">{{ __('app.layout.change') }}</a>
                                @else
                                    {{ __('app.layout.no_active_world') }}
                                @endif
                                @if(auth()->user() && auth()->user()->isChef()) <span class="chef-badge">CHEF</span> @endif
                            </div>
                        </div>
                    </header>
                    <main class="content @if(request()->routeIs('manage.index'))content--dashboard @endif">
                        @if (session('success'))
                            <div class="flash" role="status" aria-live="polite">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="errors" role="alert" aria-live="assertive">
                                <strong>{{ __('app.layout.errors_label') }}</strong>
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
        @endif
        @unless(request()->routeIs('manage.index'))<style>
            /* Shared folio theme for every management screen. */
            body { color:var(--paper-ink); font-family:"Cormorant Garamond",serif; background-color:var(--stone-1); }
            .main > header { background:linear-gradient(110deg,var(--stone-1),var(--stone-2)); border-bottom:2px solid var(--gold); }
            .main > header::before,.main > header::after { display:none; }
            .main > header .title { color:var(--paper); font:700 clamp(1.2rem,2.1vw,1.65rem)/1.2 "Cinzel",serif; }
            .analytics-wordmark { display:block; color:var(--ink); font:700 22px/1.2 "Cinzel",serif; letter-spacing:.32em; text-align:center; }
            .analytics-wordmark::after { content:"──── ✦ ────"; display:block; margin-top:7px; color:var(--gold); font:600 11px/1 "Cinzel",serif; letter-spacing:.08em; }
            .sidebar { background:var(--paper); }
            .sidebar > .nav-group > .nav-link,
            .sidebar > .nav-accordion > summary.nav-link,
            .sidebar-bottom > .nav-link { width:100%; min-height:0; margin:0; padding:12px 28px; border:0; border-radius:0; color:var(--ink); font:500 13px/1.3 "Cinzel",serif; letter-spacing:.16em; text-align:left; text-transform:uppercase; transition:padding-left 180ms ease,color 180ms ease,background 180ms ease; }
            .sidebar > .nav-accordion > summary.nav-link { display:flex; flex-direction:row; align-items:center; justify-content:space-between; color:var(--soft); }
            .sidebar .nav-accordion-body { padding:0; }
            .sidebar .nav-accordion-body .nav-link { width:100%; min-height:0; margin:0; padding:9px 28px 9px 46px; border:0; border-radius:0; color:var(--ink); font:500 12px/1.3 "Cinzel",serif; letter-spacing:.13em; text-transform:uppercase; }
            .sidebar .nav-link:hover { padding-left:34px; color:var(--accent); background:transparent; box-shadow:none; transform:none; }
            .sidebar .nav-accordion-body .nav-link:hover { padding-left:52px; }
            .sidebar .nav-link.active { position:relative; padding-left:28px; color:var(--accent); font-weight:700; background:transparent; box-shadow:none; }
            .sidebar .nav-accordion-body .nav-link.active { padding-left:46px; }
            .sidebar .nav-link.active::before { content:""; position:absolute; left:0; top:50%; width:14px; height:40px; transform:translateY(-50%); background:var(--gold-dark); clip-path:polygon(0 0,100% 0,100% 100%,50% 78%,0 100%); }
            .sidebar .nav-accordion-body .nav-link.active::before { left:0; }
            .sidebar .nav-accordion > summary.nav-link.active { color:var(--soft); font-weight:500; background:transparent; }
            .sidebar .nav-accordion > summary.nav-link.active::before { display:none; }
            .sidebar .nav-accordion > summary.nav-link.active .nav-name { text-decoration:none; font-weight:500; }
            .sidebar .nav-accordion > summary.nav-link.active:hover { color:var(--accent); }
            .sidebar .sidebar-bottom { border-top:1px dashed var(--gold); }
            .sidebar-bottom form { margin-top:4px; border-top:0; }
            .chef-badge { display:inline-block; margin-left:8px; padding:2px 6px; border:1px solid var(--gold); background:var(--stone-1); color:var(--gold); font:600 10px/1.2 "Cinzel",serif; letter-spacing:.24em; }
            .memorial-secret { display:grid; place-items:center; width:38px; height:38px; margin:8px auto 0; padding:4px; border:0; background:transparent; color:var(--gold); opacity:.5; touch-action:manipulation; }
            .memorial-secret img,.memorial-secret svg { width:30px; height:30px; }
            .memorial-secret.wiggle { animation: memorial-wiggle 120ms ease-in-out; }
            @keyframes memorial-wiggle { 50% { transform:rotate(7deg); } }
            @media(prefers-reduced-motion:reduce) { .memorial-secret.wiggle { animation:none; } }
            .sidebar-bottom form button.back-portals-link { width:100%; padding:9px 28px; color:var(--soft); font:italic 16px/1.3 "Cormorant Garamond",serif; letter-spacing:0; text-transform:none; }
            .sidebar :focus-visible,.main :focus-visible { outline:2px solid var(--accent); outline-offset:3px; }
            .content { color:var(--ink); font-family:"Cormorant Garamond",serif; }
            .content h1,.content h2,.content h3,.content h4,.content legend { color:var(--ink); font-family:"Cinzel",serif; }
            .content .card,.content .panel,.content .dashboard-card,.content .stat-card,.content .world-card,.content .first-world,.content .sheet,.content .journal-cover { border-radius:3px; border-color:var(--gold-dark); background-color:rgba(255,255,255,.35); background-image:none; box-shadow:3px 4px 0 rgba(59,45,28,.28); }
            .content .card,.content .panel,.content .dashboard-card,.content .world-card { outline:1px solid var(--gold); outline-offset:-5px; }
            .content .btn,.content .btn-mini,.content button:not(.sidebar-toggle):not(.sidebar-close),.content input[type="submit"] { min-height:36px; padding:9px 16px; border:1px solid var(--gold-dark); border-radius:2px; background:var(--gold-dark); color:var(--paper); box-shadow:2px 3px 0 rgba(59,45,28,.35); font:600 11px/1.2 "Cinzel",serif; letter-spacing:.16em; text-decoration:none; text-transform:uppercase; transition:transform 180ms ease,box-shadow 180ms ease,background 180ms ease; }
            .content .btn:hover,.content .btn-mini:hover,.content button:not(.sidebar-toggle):not(.sidebar-close):hover,.content input[type="submit"]:hover { transform:translate(1px,1px); box-shadow:1px 2px 0 rgba(40,15,10,.25); }
            .content .btn.secondary,.content .btn-mini.secondary { background:transparent; color:var(--gold-dark); box-shadow:none; }
            .content .btn.secondary:hover,.content .btn-mini.secondary:hover { background:rgba(202,164,106,.12); box-shadow:none; }
            .content .btn.danger,.content .btn-mini.danger { background:transparent; color:var(--danger); border-color:var(--danger); box-shadow:none; }
            .content .field label,.content label { color:var(--soft); font:600 12px/1.35 "Cinzel",serif; letter-spacing:.08em; }
            .content .field input,.content .field select,.content .field textarea,.content input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]),.content select,.content textarea { border:1px solid color-mix(in srgb,var(--gold-dark) 50%,transparent); border-radius:2px; background:rgba(255,255,255,.35); color:var(--ink); font-family:"Cormorant Garamond",serif; }
            .content .muted,.content .text-muted { color:var(--soft); }
            .content table th { color:var(--soft); font:600 11px/1.35 "Cinzel",serif; letter-spacing:.12em; text-transform:uppercase; }
            .content .flash { border-color:var(--emerald); border-radius:3px; background:rgba(255,255,255,.35); color:var(--emerald); }
            .content .errors { border-color:var(--danger); border-radius:3px; background:rgba(255,255,255,.35); color:var(--danger); }
            body { color:var(--paper); background:linear-gradient(180deg,var(--stone-2),var(--stone-1)); }
            body::before,body::after { display:none; }
            .wrap { border:1px solid var(--gold-dark); border-radius:3px; background:var(--paper); box-shadow:none; }
            .sidebar { background:var(--paper); color:var(--paper-ink); }
            .main > header { color:var(--paper); }
            .content { min-height:calc(100vh - var(--header-height)); background:var(--paper); color:var(--paper-ink); }
            .content::before,.content::after { display:none!important; }
            .sidebar .sidebar-bottom { border-top:0; }
            .sidebar-bottom form { margin-top:4px; padding-top:4px; border-top:1px dashed var(--gold); }
            .content time,.content .users-date,.content .home-count { font-variant-numeric:lining-nums tabular-nums; }
            .main > header :focus-visible { outline-color:var(--gold); }
            @media(prefers-reduced-motion:reduce) { *,*::before,*::after { animation:none!important; scroll-behavior:auto!important; transition:none!important; } }
            @media(max-width:700px) {
                .sidebar > .nav-group > .nav-link,.sidebar > .nav-accordion > summary.nav-link,.sidebar-bottom > .nav-link { padding:11px 20px; }
                .sidebar .nav-accordion-body .nav-link { padding:9px 20px 9px 36px; }
                .sidebar .nav-accordion-body .nav-link:hover { padding-left:42px; }
            }
        </style>@endunless
        @if(request()->routeIs('manage.index'))
        <script>(function(){var b=document.querySelector('.tg'),r=document.documentElement;if(!b)return;function paint(t){r.dataset.theme=t;b.setAttribute('aria-checked',String(t==='dark'));b.querySelector('span').textContent=t==='dark'?'Nuit':'Jour';}paint(r.dataset.theme);b.addEventListener('click',function(){var t=r.dataset.theme==='dark'?'light':'dark';paint(t);document.cookie='theme='+t+'; path=/; max-age=31536000; SameSite=Lax';fetch(@json(route('manage.preferences.theme')),{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':@json(csrf_token()),'Accept':'application/json'},body:JSON.stringify({theme:t})}).catch(function(){});});})();</script>
        @endif
        <script>
        (function(){var b=document.querySelector('[data-theme-toggle]');if(!b)return;var root=document.documentElement;function paint(t){root.dataset.theme=t;b.setAttribute('aria-checked',String(t==='light'));b.querySelector('[data-theme-label]').textContent=t==='light'?'Jour':'Nuit';var m=document.querySelector('[data-theme-color]');if(m)m.content=t==='light'?'#efe2c9':'#111723';}paint(root.dataset.theme);b.addEventListener('click',function(){var t=root.dataset.theme==='light'?'dark':'light';paint(t);document.cookie='theme='+t+'; path=/; max-age=31536000; SameSite=Lax';fetch(@json(route('manage.preferences.theme')),{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':@json(csrf_token()),'Accept':'application/json'},body:JSON.stringify({theme:t})}).catch(function(){});});})();
        </script>
        @yield('head')
        <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
        @yield('page-styles')
        @unless(request()->routeIs('manage.index'))<link rel="stylesheet" href="{{ asset('css/manage-theme.css') }}?v=20261109-5">@endunless
        <script>
            (function () {
                const body = document.body;
                const chrome = document.querySelector('.chrome');
                const desktopToggle = document.getElementById('sidebar-toggle');
                const mobileToggle = document.getElementById('mobile-nav-toggle');
                const mobileClose = document.getElementById('sidebar-close');
                const drawerBackdrop = document.getElementById('drawer-backdrop');
                if (!chrome) return;

                const sidebarKey = 'loreroom_manage_sidebar_collapsed';

                const isMobile = function () {
                    return window.matchMedia('(max-width: 980px)').matches;
                };

                const setDrawer = function (open) {
                    body.classList.toggle('manage-ui-drawer-open', open);
                    body.classList.toggle('manage-ui-locked', open && isMobile());
                    if (mobileToggle) {
                        mobileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                    }
                };

                if (!isMobile() && localStorage.getItem(sidebarKey) === '1') {
                    chrome.classList.add('is-collapsed');
                }

                if (desktopToggle) {
                    desktopToggle.addEventListener('click', function () {
                        chrome.classList.toggle('is-collapsed');
                        localStorage.setItem(sidebarKey, chrome.classList.contains('is-collapsed') ? '1' : '0');
                    });
                }

                if (mobileToggle) {
                    mobileToggle.addEventListener('click', function () {
                        setDrawer(!body.classList.contains('manage-ui-drawer-open'));
                    });
                }

                if (mobileClose) {
                    mobileClose.addEventListener('click', function () {
                        setDrawer(false);
                    });
                }

                if (drawerBackdrop) {
                    drawerBackdrop.addEventListener('click', function () {
                        setDrawer(false);
                    });
                }

                window.addEventListener('resize', function () {
                    if (!isMobile()) {
                        setDrawer(false);
                    }
                });

                window.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        setDrawer(false);
                    }
                });
            })();
        </script>
        @include('partials.i18n')
        @include('components.first-visit-tour', ['autoStart' => request()->routeIs('manage.index')])
        <script src="{{ asset('js/tour.js') }}" defer></script>
    </body>
</html>

