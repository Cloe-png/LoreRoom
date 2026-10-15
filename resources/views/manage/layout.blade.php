<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', __('app.layout.default_title'))</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;500&family=Cinzel:wght@400;600;700&family=Cormorant+Garamond:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --ink: #2b1d14;
                --paper: #f1e6cc;
                --paper-2: #ecd8b2;
                --accent: #6b1e2a;
                --leather: #1b1511;
                --line-blue: rgba(73, 120, 177, 0.26);
                --line-red: rgba(188, 72, 72, 0.4);
                --museum-1: #101722;
                --museum-2: #1a2432;
                --gold: #a8842f;
                --gold-line: rgba(228, 191, 127, 0.34);
                --nav-1: #f2c879;
                --nav-2: #8dc7b0;
                --nav-3: #93add9;
                --nav-4: #e3a0a0;
                --nav-5: #c9b1e4;
                --sidebar-width: 290px;
                --header-height: 72px;
            }
            * { box-sizing: border-box; }
            html, body { min-height: 100%; }
            body {
                margin:0;
                color:#f1e7d3;
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
            body { background-color:var(--leather); background-image:radial-gradient(ellipse at center, transparent 42%, rgba(8,5,5,.7) 100%), repeating-linear-gradient(37deg,rgba(255,255,255,.018) 0 1px,transparent 1px 5px),linear-gradient(145deg,#523832,#241817 72%); }
            .wrap { width:100%; min-height:100vh; margin:0; border:0; border-radius:0; background:transparent; box-shadow:none; }
            .chrome { min-height:100vh; }
            .sidebar { background-color:#e9d8b8; background-image:repeating-linear-gradient(0deg,transparent 0 27px,rgba(61,41,27,.09) 27px 28px),linear-gradient(90deg,rgba(61,41,27,.12),transparent 10px),url("data:image/svg+xml,%3Csvg viewBox='0 0 180 180' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.035'/%3E%3C/svg%3E"); }
            .brand-wordmark { position:relative; display:flex; align-items:baseline; gap:.16em; margin:0; color:#38271e; font:700 1.45rem/1 "Cinzel",serif; letter-spacing:.12em; white-space:nowrap; }
            .brand-wordmark i { align-self:flex-start; margin-left:2px; color:#8b6938; font-size:.58rem; font-style:normal; }
            .nav-link,.back-portals-link { width:100%; min-height:32px; flex-direction:row; align-items:center; text-align:left; justify-content:flex-start; padding:6px 10px 6px 14px; margin:0 0 3px; border-radius:2px; font-family:"Cormorant Garamond","EB Garamond","Garamond",Georgia,serif; font-size:1rem; }
            .nav-link.active { color:var(--accent); background:linear-gradient(90deg,var(--accent) 0 4px,rgba(107,30,42,.1) 4px); transform:none; }
            .nav-link.active .nav-name { text-decoration:none; font-weight:700; }
            .nav-accordion-body { padding-left:10px; }
            .sidebar-bottom .nav-link, .sidebar-bottom form { margin:0; }
            .sidebar-bottom form button { font:inherit; }
            .sidebar-bottom { border-top:1px dashed rgba(111,86,57,.5); }
            header { background:#3a2823; border-bottom:1px solid rgba(228,191,127,.4); }
            .title { font-family:"Cinzel", "Cormorant Garamond",serif; }
            .sidebar-toggle { width:28px; height:28px; border-radius:2px; border-color:rgba(228,191,127,.38); background:transparent; }
            .topline { display:inline-block; padding:6px 9px; border:1px solid rgba(228,191,127,.58); color:#f1dfbd; border-radius:2px; }
            .topline a { color:#fff0d0; }
            .content { flex:1; background-color:var(--paper); background-image:repeating-linear-gradient(0deg,transparent 0 27px,rgba(73,120,177,.12) 27px 28px),url("data:image/svg+xml,%3Csvg viewBox='0 0 180 180' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E"); }
            .content::before { left:42px; width:3px; background:rgba(147,39,52,.62); }
            .content::after { background:radial-gradient(circle at 50% 4px,#49362d 0 4px,#967e66 4.5px 5px,transparent 5.5px 22px),radial-gradient(circle at 50% 26px,#49362d 0 4px,#967e66 4.5px 5px,transparent 5.5px 22px); opacity:.9; }
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
                color: #694d2d;
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
                color: #5e472c;
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
                color: #6b4b2a;
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
                color: #2f2418;
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
                color: #2f2418;
                border-radius: 6px;
                padding: 4px 8px;
                font-size: .78rem;
                font-family: "Segoe Print","Comic Sans MS",cursive;
                text-decoration: none;
                display: inline-block;
            }
            .btn-mini.danger { background: rgba(236, 184, 184, .85); color:#4a1f1f; }
            .btn-mini.secondary { background: rgba(189, 208, 237, .85); color:#1f2a3d; }
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
                color:#f6e8cb;
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
                color:#f6e8cb;
                min-width: 0;
            }
            .topline {
                color:#dbc9a8;
                font-size:.76rem;
                letter-spacing:.1em;
                text-transform:uppercase;
            }
            .topline a {
                color:#f6e8cb;
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
                color: #f2e5ca;
                text-decoration: none;
                font-size: .78rem;
                letter-spacing: .05em;
                text-transform: uppercase;
                white-space: nowrap;
            }
            .glance-chip strong {
                color: #fff2d4;
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
                color:#191410;
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
            /* Final editorial details for the folio dashboard. */
            .nav-link,.back-portals-link { box-sizing:border-box; width:100%; min-height:0; padding:15px 28px; margin:0; border:0; border-radius:0; color:#30251d; font:400 13px/1.25 "Cinzel",serif; letter-spacing:.16em; text-align:left; text-transform:uppercase; transition:padding-left 180ms ease,color 180ms ease,background 180ms ease; }
            .nav-link { display:flex; flex-direction:row; align-items:center; justify-content:flex-start; }
            .nav-link:hover,.back-portals-link:hover { padding-left:34px; color:var(--accent); text-decoration:none; background:transparent; box-shadow:none; transform:none; }
            .nav-link.active { position:relative; padding-left:28px; color:var(--accent); font-weight:700; background:linear-gradient(90deg,rgba(107,30,42,.1),transparent); box-shadow:none; }
            .nav-link.active::before { content:""; position:absolute; left:0; top:50%; width:14px; height:44px; transform:translateY(-50%); background:var(--accent); clip-path:polygon(0 0,100% 0,100% 100%,50% 78%,0 100%); }
            .nav-link.active .nav-name { font-weight:700; text-decoration:none; }
            .nav-accordion-body { padding:0; }
            .sidebar-bottom { padding-top:12px; }
            .sidebar-bottom form button.back-portals-link { font:400 13px/1.25 "Cinzel",serif; letter-spacing:.16em; text-transform:uppercase; }
            header { background:linear-gradient(110deg,#2a1f18,#1f1712); border-bottom:2px solid var(--gold); }
            .sidebar-toggle { width:38px; height:38px; border:1px solid var(--gold); border-radius:2px; background:transparent; }
            .content::before { width:2px; background:#b4535b; }
            .content::after { background:radial-gradient(circle at 50% 7px,#17110e 0 5px,var(--gold) 5.5px 7px,transparent 7.5px); background-size:22px 46px; background-repeat:repeat-y; opacity:1; }
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
        </style>
    </head>
    <body>
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
                                <span class="analytics-wordmark">LOREROOM</span>
                            @else
                                @include('partials.brand-logo', ['size' => 80])
                            @endif
                        </h1>
                    </div>
                    <div class="nav-group">
                        <a class="nav-link @if(request()->routeIs('manage.index')) active @endif" href="{{ route('manage.index') }}">
                            <span class="nav-name">{{ __('app.nav.home') }}</span>
                        </a>
                    </div>

                    <details class="nav-accordion" @if(request()->routeIs('manage.worlds.*') || request()->routeIs('manage.characters.*') || request()->routeIs('manage.relations.*') || request()->routeIs('manage.genealogy.*') || request()->routeIs('manage.gallery.*') || request()->routeIs('manage.species.*') || request()->routeIs('manage.lore.*') || request()->routeIs('manage.jobs.*') || request()->routeIs('manage.factions.*') || request()->routeIs('manage.chapters.*') || request()->routeIs('manage.places.*')) open @endif>
                        <summary class="nav-link @if(request()->routeIs('manage.worlds.*') || request()->routeIs('manage.characters.*') || request()->routeIs('manage.relations.*') || request()->routeIs('manage.genealogy.*') || request()->routeIs('manage.gallery.*') || request()->routeIs('manage.species.*') || request()->routeIs('manage.lore.*') || request()->routeIs('manage.jobs.*') || request()->routeIs('manage.factions.*') || request()->routeIs('manage.chapters.*') || request()->routeIs('manage.places.*')) active @endif">
                            <span class="nav-name">Gestion</span>
                        </summary>
                        <div class="nav-accordion-body">
                            <a class="nav-link @if(request()->routeIs('manage.worlds.*')) active @endif" href="{{ route('manage.worlds.index') }}">
                                <span class="nav-name">{{ __('app.nav.worlds') }}</span>
                            </a>
                            <a class="nav-link @if(request()->routeIs('manage.characters.*')) active @endif" href="{{ route('manage.characters.index') }}">
                                <span class="nav-name">{{ __('app.nav.characters') }}</span>
                            </a>
                            <a class="nav-link @if(request()->routeIs('manage.places.*')) active @endif" href="{{ route('manage.places.index') }}">
                                <span class="nav-name">{{ __('app.nav.places') }}</span>
                            </a>
                            @if($currentUser && $currentUser->canAccessModule('chapters'))
                                <a class="nav-link @if(request()->routeIs('manage.chapters.*')) active @endif" href="{{ route('manage.chapters.index') }}">
                                    <span class="nav-name">{{ __('app.nav.chapters') }}</span>
                                </a>
                            @endif
                            @if($currentUser && $currentUser->canAccessModule('lore'))
                                <a class="nav-link @if(request()->routeIs('manage.lore.*')) active @endif" href="{{ route('manage.lore.index') }}">
                                    <span class="nav-name">{{ __('app.nav.lore') }}</span>
                                </a>
                            @endif
                            @if($currentUser && $currentUser->canAccessModule('jobs'))
                                <a class="nav-link @if(request()->routeIs('manage.jobs.*')) active @endif" href="{{ route('manage.jobs.index') }}">
                                    <span class="nav-name">{{ __('app.nav.jobs') }}</span>
                                </a>
                            @endif
                            @if($currentUser && $currentUser->canAccessModule('factions'))
                                <a class="nav-link @if(request()->routeIs('manage.factions.*')) active @endif" href="{{ route('manage.factions.index') }}">
                                    <span class="nav-name">{{ __('app.nav.factions') }}</span>
                                </a>
                            @endif
                            @if($currentUser && $currentUser->canAccessModule('lore'))
                                <a class="nav-link @if(request()->routeIs('manage.species.*')) active @endif" href="{{ route('manage.species.index') }}">
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
                            <a class="nav-link @if(request()->routeIs('manage.chronicles.*')) active @endif" href="{{ route('manage.chronicles.index') }}">
                                <span class="nav-name">{{ __('app.nav.chronicles') }}</span>
                            </a>
                            <a class="nav-link @if(request()->routeIs('manage.relations.*')) active @endif" href="{{ route('manage.relations.index') }}">
                                <span class="nav-name">{{ __('app.nav.relations') }}</span>
                            </a>
                            @if($currentUser && $currentUser->canAccessModule('genealogy'))
                                <a class="nav-link @if(request()->routeIs('manage.genealogy.*')) active @endif" href="{{ route('manage.genealogy.index') }}">
                                    <span class="nav-name">{{ __('app.nav.genealogy') }}</span>
                                </a>
                            @endif
                            @if($currentUser && $currentUser->canAccessModule('gallery'))
                                <a class="nav-link @if(request()->routeIs('manage.gallery.*')) active @endif" href="{{ route('manage.gallery.index') }}">
                                    <span class="nav-name">{{ __('app.nav.gallery') }}</span>
                                </a>
                            @endif
                        </div>
                    </details>

                    <details class="nav-accordion" @if(request()->routeIs('manage.suggestions.*') || request()->routeIs('manage.trash.*')) open @endif>
                        <summary class="nav-link @if(request()->routeIs('manage.suggestions.*') || request()->routeIs('manage.trash.*')) active @endif">
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
                            <a class="nav-link @if(request()->routeIs('manage.analytics.*')) active @endif" href="{{ route('manage.analytics.index') }}">
                                <span class="nav-name">{{ __('app.nav.analytics') }}</span>
                            </a>
                        @endif
                        @if($currentUser && $currentUser->canAccessModule('users'))
                            <a class="nav-link @if(request()->routeIs('manage.users.*')) active @endif" href="{{ route('manage.users.index') }}">
                                <span class="nav-name">{{ __('app.nav.accounts') }}</span>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="back-portals-link">{{ __('app.nav.logout') }}</button>
                        </form>
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
                            <div class="topline">
                                @if(!empty($activeWorld))
                                    {{ __('app.layout.active_world', ['world' => $activeWorld->name]) }} ·
                                    <a href="{{ route('manage.worlds.index') }}">{{ __('app.layout.change') }}</a>
                                @else
                                    {{ __('app.layout.no_active_world') }}
                                @endif
                            </div>
                        </div>
                    </header>
                    <main class="content">
                        @if (session('success'))
                            <div class="flash">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="errors">
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
        <style>
            /* Shared folio theme for every management screen. */
            :root { --paper:#f1e6cc; --ink:#2b1d14; --soft:#5a4532; --accent:#6b1e2a; --gold:#a8842f; --leather:#1b1511; }
            body { color:var(--ink); font-family:"Cormorant Garamond",serif; background-color:var(--leather); }
            .main > header { background:linear-gradient(110deg,#2a1f18,#1f1712); border-bottom:2px solid var(--gold); }
            .main > header::before,.main > header::after { display:none; }
            .main > header .title { color:#f1e6cc; font:700 clamp(1.2rem,2.1vw,1.65rem)/1.2 "Cinzel",serif; }
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
            .sidebar .nav-link.active { position:relative; padding-left:28px; color:var(--accent); font-weight:700; background:linear-gradient(90deg,rgba(107,30,42,.1),transparent); box-shadow:none; }
            .sidebar .nav-accordion-body .nav-link.active { padding-left:46px; }
            .sidebar .nav-link.active::before { content:""; position:absolute; left:0; top:50%; width:14px; height:40px; transform:translateY(-50%); background:var(--accent); clip-path:polygon(0 0,100% 0,100% 100%,50% 78%,0 100%); }
            .sidebar .nav-accordion-body .nav-link.active::before { left:0; }
            .sidebar .nav-accordion > summary.nav-link.active { color:var(--soft); font-weight:500; background:transparent; }
            .sidebar .nav-accordion > summary.nav-link.active::before { display:none; }
            .sidebar .nav-accordion > summary.nav-link.active .nav-name { text-decoration:none; font-weight:500; }
            .sidebar .nav-accordion > summary.nav-link.active:hover { color:var(--accent); }
            .sidebar .sidebar-bottom { border-top:1px dashed rgba(168,132,47,.65); }
            .sidebar-bottom form { margin-top:4px; border-top:1px dashed rgba(168,132,47,.48); }
            .sidebar-bottom form button.back-portals-link { width:100%; padding:9px 28px; color:var(--soft); font:italic 16px/1.3 "Cormorant Garamond",serif; letter-spacing:0; text-transform:none; }
            .sidebar :focus-visible,.main :focus-visible { outline:2px solid var(--accent); outline-offset:3px; }
            .content { color:var(--ink); font-family:"Cormorant Garamond",serif; }
            .content h1,.content h2,.content h3,.content h4,.content legend { color:var(--ink); font-family:"Cinzel",serif; }
            .content .card,.content .panel,.content .dashboard-card,.content .stat-card,.content .world-card,.content .first-world,.content .sheet,.content .journal-cover { border-radius:3px; border-color:#9a7f48; background-color:rgba(255,250,236,.55); background-image:none; box-shadow:3px 4px 0 rgba(70,45,20,.22); }
            .content .card,.content .panel,.content .dashboard-card,.content .world-card { outline:1px solid #c7ac72; outline-offset:-5px; }
            .content .btn,.content .btn-mini,.content button:not(.sidebar-toggle):not(.sidebar-close),.content input[type="submit"] { min-height:36px; padding:9px 16px; border:1px solid var(--accent); border-radius:2px; background:var(--accent); color:#f6e7c4; box-shadow:2px 3px 0 rgba(40,15,10,.35); font:600 11px/1.2 "Cinzel",serif; letter-spacing:.16em; text-decoration:none; text-transform:uppercase; transition:transform 180ms ease,box-shadow 180ms ease,background 180ms ease; }
            .content .btn:hover,.content .btn-mini:hover,.content button:not(.sidebar-toggle):not(.sidebar-close):hover,.content input[type="submit"]:hover { transform:translate(1px,1px); box-shadow:1px 2px 0 rgba(40,15,10,.25); }
            .content .btn.secondary,.content .btn-mini.secondary { background:transparent; color:var(--accent); box-shadow:none; }
            .content .btn.secondary:hover,.content .btn-mini.secondary:hover { background:rgba(107,30,42,.06); box-shadow:none; }
            .content .btn.danger,.content .btn-mini.danger { background:transparent; color:var(--accent); box-shadow:none; }
            .content .field label,.content label { color:var(--soft); font:600 12px/1.35 "Cinzel",serif; letter-spacing:.08em; }
            .content .field input,.content .field select,.content .field textarea,.content input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]),.content select,.content textarea { border:1px solid rgba(90,69,50,.5); border-radius:2px; background:rgba(255,250,236,.7); color:var(--ink); font-family:"Cormorant Garamond",serif; }
            .content .muted,.content .text-muted { color:var(--soft); }
            .content table th { color:var(--soft); font:600 11px/1.35 "Cinzel",serif; letter-spacing:.12em; text-transform:uppercase; }
            .content .flash { border-color:#537b52; border-radius:3px; background:#e6eddc; color:#294329; }
            .content .errors { border-color:#8b3540; border-radius:3px; background:#f1dfd8; color:#5a1d27; }
            @media(max-width:700px) {
                .sidebar > .nav-group > .nav-link,.sidebar > .nav-accordion > summary.nav-link,.sidebar-bottom > .nav-link { padding:11px 20px; }
                .sidebar .nav-accordion-body .nav-link { padding:9px 20px 9px 36px; }
                .sidebar .nav-accordion-body .nav-link:hover { padding-left:42px; }
            }
        </style>
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
    </body>
</html>

