@extends('manage.layout')

@section('title', __('app.analytics.page_title'))
@section('header', __('app.analytics.header'))

@section('content')
    <style>
        .analytics-shell {
            display: grid;
            gap: 14px;
        }

        .analytics-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(114, 84, 49, .34);
            border-radius: 14px;
            padding: 20px 18px;
            background:
                radial-gradient(580px 210px at 8% -10%, rgba(255, 235, 194, .82), transparent 62%),
                linear-gradient(180deg, rgba(248, 237, 215, .97), rgba(230, 210, 173, .95));
            box-shadow: 0 16px 34px rgba(67, 45, 20, .16);
        }

        .analytics-hero::after {
            content: "";
            position: absolute;
            right: -34px;
            top: -48px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(194, 147, 86, .22), rgba(194, 147, 86, 0));
            pointer-events: none;
        }

        .analytics-hero-head {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .analytics-title {
            margin: 0;
            color: #4a3117;
            font-family: "Cinzel", "Times New Roman", serif;
            letter-spacing: .04em;
            font-size: clamp(1.25rem, 2vw, 1.8rem);
        }

        .analytics-text {
            margin: 8px 0 0;
            color: #6b5031;
            max-width: 760px;
            line-height: 1.5;
        }

        .analytics-range {
            border: 1px dashed rgba(104, 73, 40, .45);
            border-radius: 999px;
            background: rgba(255, 246, 226, .58);
            color: #51371d;
            padding: 7px 12px;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
        }

        .analytics-filter-panel {
            padding: 16px;
        }

        .analytics-filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: end;
        }

        .analytics-filter-field {
            min-width: 220px;
            margin: 0;
        }

        .analytics-inline-form {
            display: flex;
            gap: 10px;
            align-items: end;
            flex-wrap: wrap;
            padding: 16px;
        }

        .analytics-inline-form .field {
            margin: 0;
            min-width: 240px;
            flex: 1 1 320px;
        }

        .analytics-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .analytics-card {
            border: 1px solid rgba(109, 78, 45, .24);
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 10px 22px rgba(50, 34, 17, .1);
            min-height: 120px;
        }

        .analytics-card:nth-child(1) { background: linear-gradient(180deg, #f8dfa3, #ebc271); }
        .analytics-card:nth-child(2) { background: linear-gradient(180deg, #c6e2d5, #9dc9b7); }
        .analytics-card:nth-child(3) { background: linear-gradient(180deg, #c6d7ef, #a5bee2); }
        .analytics-card:nth-child(4) { background: linear-gradient(180deg, #e8c8c8, #dca6a6); }
        .analytics-card:nth-child(5) { background: linear-gradient(180deg, #ead9b7, #d7bc8a); }
        .analytics-card:nth-child(6) { background: linear-gradient(180deg, #d7e0bf, #b7c988); }
        .analytics-card:nth-child(7) { background: linear-gradient(180deg, #f0d6bc, #ddb08f); }
        .analytics-card:nth-child(8) { background: linear-gradient(180deg, #d7d0ea, #b8acd9); }

        .analytics-card-label {
            color: #5f4628;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .analytics-card-value {
            margin-top: 8px;
            color: #2e2113;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 2rem;
            line-height: 1;
        }

        .analytics-card-note {
            margin-top: 10px;
            color: #594129;
            font-size: .9rem;
            line-height: 1.35;
        }

        .analytics-columns {
            display: grid;
            gap: 12px;
            grid-template-columns: 1.2fr 1fr;
        }

        .analytics-panel {
            padding: 0;
            overflow: hidden;
            border-radius: 14px;
        }

        .analytics-panel-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 14px 16px 12px;
            border-bottom: 1px solid rgba(114, 84, 49, .18);
            background: linear-gradient(180deg, rgba(255,255,255,.24), rgba(255,255,255,.1));
        }

        .analytics-panel-title {
            margin: 0;
            color: #56391b;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1rem;
            letter-spacing: .04em;
        }

        .analytics-panel-meta {
            color: #75583a;
            font-size: .86rem;
        }

        .analytics-table-wrap {
            overflow-x: auto;
            padding: 0 16px 12px;
        }

        .analytics-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 520px;
        }

        .analytics-table thead th {
            background: rgba(241, 228, 201, .96);
            box-shadow: inset 0 -1px 0 rgba(114, 84, 49, .16);
        }

        .analytics-table td,
        .analytics-table th {
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }

        .analytics-table tbody tr:hover {
            background: rgba(255,255,255,.16);
        }

        .analytics-strong {
            color: #3f2a17;
            font-weight: 700;
        }

        .analytics-soft {
            color: #6a4d30;
            word-break: break-word;
        }

        .analytics-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: .76rem;
            letter-spacing: .05em;
            text-transform: uppercase;
            border: 1px solid rgba(95, 69, 37, .2);
            background: rgba(255,255,255,.54);
            color: #6e512f;
        }

        .analytics-empty {
            padding: 18px 16px 20px;
        }

        @media (max-width: 1180px) {
            .analytics-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .analytics-columns {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .analytics-grid {
                grid-template-columns: 1fr;
            }

            .analytics-filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .analytics-filter-form .btn {
                width: 100%;
                text-align: center;
            }

            .analytics-inline-form {
                flex-direction: column;
                align-items: stretch;
            }

            .analytics-inline-form .btn {
                width: 100%;
                text-align: center;
            }
        }
        :root { --paper:#f1e6cc; --ink:#2b1d14; --soft:#5a4532; --accent:#6b1e2a; --gold:#a8842f; --leather:#1b1511; }
        .analytics-shell { position:relative; gap:20px; }
        .analytics-hero,.analytics-card,.analytics-panel { border:1px solid #9a7f48; border-radius:3px; outline:1px solid #c7ac72; outline-offset:-5px; background:rgba(255,250,236,.55); background-image:none; box-shadow:3px 4px 0 rgba(70,45,20,.22); }
        .analytics-hero { min-height:136px; padding:22px 24px; }
        .analytics-hero::after { display:none; }
        .analytics-hero-head { align-items:center; }
        .analytics-title { color:var(--ink); font:700 26px/1.2 "Cinzel",serif; }
        .analytics-text { max-width:760px; color:var(--soft); font:400 18px/1.4 "Cormorant Garamond",serif; }
        .analytics-range { position:absolute; right:24px; top:68px; display:block; width:max-content; margin:0; padding:0; border:0; border-radius:0; background:none; color:var(--soft); font:600 11px/1.4 "Cinzel",serif; letter-spacing:.24em; }
        .content .analytics-shell .analytics-filter-panel { position:absolute; z-index:3; top:16px; right:24px; margin:0; padding:0; border:0; outline:0; background:transparent; box-shadow:none; }
        .analytics-filter-form { display:flex; flex-wrap:nowrap; align-items:center; gap:10px; }
        .analytics-filter-field { min-width:190px; }
        .analytics-filter-field label { position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border:0; }
        .analytics-filter-field select { min-height:38px; padding:6px 10px; border:1px solid #9a7f48; border-radius:2px; background:var(--paper); color:var(--ink); font:400 16px "Cormorant Garamond",serif; }
        .analytics-shell .analytics-filter-form .btn { min-height:38px; padding:9px 16px; border:1px solid var(--accent); border-radius:2px; background:var(--accent); color:#f6e7c4; box-shadow:2px 3px 0 rgba(40,15,10,.35); font:600 11px/1.2 "Cinzel",serif; letter-spacing:.18em; text-transform:uppercase; transition:transform 180ms ease,box-shadow 180ms ease; }
        .content .analytics-shell .analytics-filter-field select { border-color:#9a7f48; border-radius:2px; background:var(--paper); color:var(--ink); font:400 16px "Cormorant Garamond",serif; }
        .content .analytics-shell .analytics-filter-form button.btn { letter-spacing:.18em; }
        .analytics-filter-form .btn:hover { transform:translate(1px,1px); box-shadow:1px 2px 0 rgba(40,15,10,.25); }
        .analytics-group { display:grid; gap:12px; }
        .analytics-group-title { display:flex; align-items:center; gap:12px; margin:0; color:var(--soft); font:600 11px/1.3 "Cinzel",serif; letter-spacing:.24em; text-transform:uppercase; }
        .analytics-group-title::after { content:"❦"; display:flex; align-items:center; gap:10px; flex:1; color:var(--gold); font-size:13px; background:linear-gradient(var(--gold),var(--gold)) center/100% 1px no-repeat; }
        .analytics-grid { grid-template-columns:repeat(4,minmax(0,1fr)); gap:20px; }
        .analytics-card { min-width:0; min-height:142px; padding:18px; }
        .analytics-card-label { color:var(--soft); font:600 11px/1.4 "Cinzel",serif; letter-spacing:.24em; text-transform:uppercase; }
        .analytics-card-value { margin-top:14px; color:var(--ink); font:700 44px/1 "Cinzel",serif; font-variant-numeric:lining-nums tabular-nums; }
        .analytics-card-value.analytics-error { color:var(--accent); }
        .analytics-card-note { margin-top:11px; color:var(--soft); font:italic 16px/1.3 "Cormorant Garamond",serif; }
        .analytics-columns { grid-template-columns:minmax(0,1.2fr) minmax(0,1fr); gap:20px; align-items:start; }
        .analytics-panel { min-width:0; padding:0; overflow:hidden; }
        .analytics-panel-top { padding:15px 18px 12px; border-bottom:1px solid #c7ac72; background:#ebdcb8; }
        .analytics-panel-title { color:var(--ink); font:600 16px/1.3 "Cinzel",serif; }
        .analytics-panel-meta { color:var(--soft); font:400 15px/1.3 "Cormorant Garamond",serif; }
        .analytics-table-wrap { overflow-x:hidden; padding:0 16px 12px; }
        .analytics-table { width:100%; min-width:0; table-layout:fixed; border-collapse:collapse; font-variant-numeric:lining-nums tabular-nums; }
        .analytics-table--pages th:first-child,.analytics-table--pages td:first-child { width:75%; }
        .analytics-table--pages th:last-child,.analytics-table--pages td:last-child { width:25%; text-align:right; }
        .analytics-table thead th { background:#ebdcb8; box-shadow:none; border-bottom:1px solid var(--gold); color:var(--soft); font:600 11px/1.35 "Cinzel",serif; letter-spacing:.14em; text-transform:uppercase; }
        .analytics-table td,.analytics-table th { padding:10px 8px; }
        .analytics-table tbody td { border-bottom:1px dotted #c7ac72; }
        .analytics-table tbody tr:hover { background:rgba(168,132,47,.06); }
        .analytics-table .analytics-strong { color:var(--ink); }
        .analytics-route-label { color:var(--ink); font:500 18px/1.2 "Cormorant Garamond",serif; }
        .analytics-route-technical { display:block; color:var(--soft); font:italic 14px/1.2 "Cormorant Garamond",serif; overflow:hidden; text-overflow:ellipsis; }
        .analytics-soft { color:var(--soft); font:400 14px/1.3 "Cormorant Garamond",serif; overflow-wrap:anywhere; }
        .analytics-badge { display:inline; padding:0; border:0; border-radius:0; background:none; color:var(--ink); font:600 11px/1.4 "Cinzel",serif; letter-spacing:.14em; text-transform:uppercase; }
        .analytics-badge::before { content:"◆"; margin-right:7px; color:var(--ink); font-size:9px; }
        .analytics-action--modification::before { color:var(--gold); }
        .analytics-action--suppression::before { color:var(--accent); }
        .analytics-action--consultation::before { color:var(--ink); }
        .analytics-empty { padding:20px; color:var(--soft); }
        .analytics-columns > .analytics-panel:nth-child(2) { align-self:start; }
        .analytics-empty-state { display:grid; justify-items:center; gap:6px; padding:10px 0; text-align:center; }
        .analytics-empty-state svg { width:60px; height:60px; fill:none; stroke:var(--accent); stroke-width:1.7; stroke-linecap:round; stroke-linejoin:round; }
        .analytics-empty-state h4 { margin:0; color:var(--ink); font:600 15px/1.3 "Cinzel",serif; }
        .analytics-empty-state .analytics-empty-note { margin:0; color:var(--soft); font:400 24px/1.15 "Caveat",cursive; }
        .analytics-empty-state .analytics-subtitle { margin:0; color:var(--soft); font:italic 14px/1.3 "Cormorant Garamond",serif; }
        .analytics-inline-form { align-items:center; gap:12px; padding:14px 16px; }
        .analytics-inline-form .field { margin:0; }
        .analytics-inline-form .field label { color:var(--soft); font:600 11px/1.3 "Cinzel",serif; letter-spacing:.12em; text-transform:uppercase; }
        .analytics-inline-form input[type="text"] { height:44px; border:1px solid #9a7f48; border-radius:2px; background:var(--paper); color:var(--ink); font:400 16px "Cormorant Garamond",serif; }
        .analytics-inline-form input::placeholder { color:var(--soft); font-style:italic; opacity:1; }
        .analytics-shell .analytics-inline-form .btn { min-height:44px; border:1px solid var(--accent); border-radius:2px; background:var(--accent); color:#f6e7c4; box-shadow:2px 3px 0 rgba(40,15,10,.35); font:600 11px/1.2 "Cinzel",serif; letter-spacing:.18em; text-transform:uppercase; }
        .content .analytics-shell .analytics-inline-form button.btn { letter-spacing:.18em; }
        .analytics-inline-form .btn:hover { transform:translate(1px,1px); box-shadow:1px 2px 0 rgba(40,15,10,.25); }
        .analytics-table td,.analytics-table th,.analytics-card-value,.analytics-range { font-variant-numeric:lining-nums tabular-nums; }
        .analytics-table--actions th:nth-child(1) { width:16%; }
        .analytics-table--actions th:nth-child(2) { width:24%; }
        .analytics-table--actions th:nth-child(3) { width:25%; }
        .analytics-table--actions th:nth-child(4) { width:35%; }
        .analytics-table--actions td:nth-child(2) .analytics-strong { color:var(--ink); font:600 17px/1.2 "Cormorant Garamond",serif; }
        .analytics-table--actions td:nth-child(4) .analytics-soft { display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .analytics-table--actions .analytics-badge { white-space:normal; }
        .analytics-columns > .analytics-panel:nth-child(2) .analytics-empty > .muted { margin-top:8px!important; color:var(--soft); font:italic 14px/1.35 "Cormorant Garamond",serif; }
        .wrap { overflow:visible; }
        .sidebar { position:sticky; top:0; align-self:start; height:100vh; overflow-y:auto; }
        .sidebar .brand { min-height:104px; display:flex; justify-content:center; align-items:flex-start; padding:12px 4px 25px; }
        .sidebar .brand h1 { width:100%; }
        .analytics-wordmark { font:700 22px/1.2 "Cinzel",serif; letter-spacing:.32em; white-space:nowrap; }
        .sidebar .nav-link.active { background:linear-gradient(90deg,rgba(107,30,42,.12),transparent); }
        .content { font-variant-numeric:lining-nums tabular-nums; }
        .content::before { width:2px; background:#b4535b; }
        .content::after { background:radial-gradient(circle at 50% 6px,#2a1f18 0 4px,#8c7440 4px 6px,transparent 6.5px); background-size:22px 46px; background-repeat:repeat-y; }
        @media(max-width:1000px) { .analytics-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } .analytics-columns { grid-template-columns:1fr; } }
        @media(max-width:600px) { .analytics-hero { padding:20px; } .analytics-hero-head { align-items:stretch; flex-direction:column; } .analytics-hero-head > div:first-child { max-width:none; } .analytics-range { position:static; margin-top:10px; } .content .analytics-shell .analytics-filter-panel { position:static; margin-top:-10px; } .analytics-filter-form { flex-wrap:wrap; } .analytics-filter-field { flex:1 1 100%; } .analytics-grid { grid-template-columns:1fr; } .analytics-columns { grid-template-columns:1fr; } .analytics-table-wrap { overflow-x:hidden; } }
        .analytics-group-title::after { content:"❦"; }
        .analytics-badge::before { content:"◆"; }
        .analytics-hero-head > div:first-child { max-width:calc(100% - 370px); }
        .analytics-shell .analytics-group-title { color:var(--soft); }
        .analytics-group-title::after { text-align:center; background:linear-gradient(to right,var(--gold),var(--gold) calc(50% - 12px),transparent calc(50% - 12px),transparent calc(50% + 12px),var(--gold) calc(50% + 12px)); }
        .analytics-shell .analytics-table--actions td:nth-child(2) .analytics-strong { font-family:"Cormorant Garamond",serif; }
        html body .sidebar .nav-link.active { background:linear-gradient(90deg,rgba(107,30,42,.12),transparent); }
        @media(max-width:980px) { .sidebar { position:fixed; top:0; height:100vh; } }
    </style>

    <div class="analytics-shell">
        <section class="analytics-hero">
            <div class="analytics-hero-head">
                <div>
                    <h2 class="analytics-title">Activité et sécurité</h2>
                    <p class="analytics-text">
                        Lecture rapide des pages consultées, des actions récentes, des connexions et des activités suspectes.
                    </p>
                </div>
                <div class="analytics-range">Depuis le {{ $since->format('d/m/Y') }}</div>
            </div>
        </section>

        <section class="panel analytics-filter-panel">
            <form method="GET" action="{{ route('manage.analytics.index') }}" class="analytics-filter-form">
                <div class="field analytics-filter-field">
                    <label>Période analysée</label>
                    <select name="days">
                        <option value="7" {{ $days === 7 ? 'selected' : '' }}>7 jours</option>
                        <option value="14" {{ $days === 14 ? 'selected' : '' }}>14 jours</option>
                        <option value="30" {{ $days === 30 ? 'selected' : '' }}>30 jours</option>
                        <option value="60" {{ $days === 60 ? 'selected' : '' }}>60 jours</option>
                        <option value="90" {{ $days === 90 ? 'selected' : '' }}>90 jours</option>
                    </select>
                </div>
                <button class="btn" type="submit">Mettre à jour</button>
            </form>
        </section>

        <section class="analytics-metric-group">
            <h3 class="analytics-group-title">Utilisateurs</h3>
            <div class="analytics-grid">
            <article class="analytics-card">
                <div class="analytics-card-label">Utilisateurs</div>
                <div class="analytics-card-value">{{ $overview['totalUsers'] }}</div>
                <div class="analytics-card-note">Nombre total d'utilisateurs inscrits.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Nouveaux aujourd'hui</div>
                <div class="analytics-card-value">{{ $overview['newUsersToday'] }}</div>
                <div class="analytics-card-note">Comptes créés aujourd'hui.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Nouveaux cette semaine</div>
                <div class="analytics-card-value">{{ $overview['newUsersWeek'] }}</div>
                <div class="analytics-card-note">Comptes créés depuis le debut de semaine.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Utilisateurs actifs</div>
                <div class="analytics-card-value">{{ $overview['activeUsers'] }}</div>
                <div class="analytics-card-note">Utilisateurs distincts actifs sur la période.</div>
            </article>
            </div>
        </section>
        <section class="analytics-metric-group">
            <h3 class="analytics-group-title">Activit&eacute;</h3>
            <div class="analytics-grid">
            <article class="analytics-card">
                <div class="analytics-card-label">Connexions</div>
                <div class="analytics-card-value">{{ $overview['logins'] }}</div>
                <div class="analytics-card-note">Connexions réussies sur la période.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Actions realisées</div>
                <div class="analytics-card-value">{{ $overview['totalActions'] }}</div>
                <div class="analytics-card-note">Toutes les actions loguées sur la période.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Taux d'erreur</div>
                <div class="analytics-card-value @if($overview['errorRate'] > 0) analytics-error @endif">{{ number_format($overview['errorRate'], 1, ',', ' ') }}%</div>
                <div class="analytics-card-note">Base sur les échecs de connexion détectés.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Temps moyen passé</div>
                <div class="analytics-card-value">{{ $overview['averageTimeSpent'] }}</div>
                <div class="analytics-card-note">Estimation par session avec coupure après 30 min d'inactivité.</div>
            </article>
            </div>
        </section>

        <section class="analytics-columns">
            <section class="panel analytics-panel">
                <div class="analytics-panel-top">
                    <h3 class="analytics-panel-title">Top pages</h3>
                    <div class="analytics-panel-meta">Nom de page et nombre de consultations</div>
                </div>

                @if($topPages->isEmpty())
                    <div class="analytics-empty">
                        <p class="muted" style="margin:0;">Aucune consultation enregistrée sur cette période.</p>
                    </div>
                @else
                    <div class="analytics-table-wrap">
                        <table class="analytics-table analytics-table--pages">
                            <thead>
                                <tr>
                                    <th scope="col">Page</th>
                                    <th scope="col">Consultations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPages as $page)
                                    <tr>
                                        @php
                                            $routeName = $page->route_name ?: 'Sans nom';
                                            $routeParts = explode('.', $routeName);
                                            $sectionLabels = [
                                                'worlds' => __('app.nav.worlds'), 'characters' => __('app.nav.characters'),
                                                'places' => __('app.nav.places'), 'chapters' => __('app.nav.chapters'),
                                                'lore' => __('app.nav.lore'), 'jobs' => __('app.nav.jobs'),
                                                'factions' => __('app.nav.factions'), 'chronicles' => __('app.nav.chronicles'),
                                                'relations' => __('app.nav.relations'), 'genealogy' => __('app.nav.genealogy'),
                                                'gallery' => __('app.nav.gallery'),
                                            ];
                                            $sectionLabel = $sectionLabels[$routeParts[1] ?? ''] ?? ucfirst(str_replace('_', ' ', $routeParts[1] ?? $routeName));
                                            $routeLabel = strpos($routeName, 'manage.') === 0 ? 'Gestion ' . html_entity_decode('&middot;', ENT_QUOTES, 'UTF-8') . ' ' . $sectionLabel : $sectionLabel;
                                            $routeLabel = strpos($routeName, 'manage.') === 0 ? 'Gestion · ' . $sectionLabel : $sectionLabel;
                                        @endphp
                                        <td><span class="analytics-route-label" title="{{ $routeName }}">{{ $routeLabel }}</span><span class="analytics-route-technical">{{ $routeName }}</span></td>
                                        <td><div class="analytics-strong">{{ $page->consultations_count }}</div></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="panel analytics-panel">
                <div class="analytics-panel-top">
                    <h3 class="analytics-panel-title">Activités suspectes</h3>
                    <div class="analytics-panel-meta">{{ $overview['failedActions'] }} échec(s) de connexion sur la période</div>
                </div>

                @if($suspiciousActivities->isEmpty() && $lockedUsers->isEmpty())
                    <div class="analytics-empty">
                        <div class="analytics-empty-state">
                            <svg viewBox="0 0 60 60" aria-hidden="true"><path d="M30 5 49 12v14c0 13-8 22-19 29C19 48 11 39 11 26V12z"/><path d="m21 29 6 6 13-14"/></svg>
                            <h4>Rien &agrave; signaler</h4>
                            <p class="analytics-empty-note">Les portes sont closes, le calme r&egrave;gne&hellip;</p>
                        </div>
                        <p class="muted" style="margin:0;">Aucun comportement suspect détecté sur cette période.</p>
                    </div>
                @else
                    <div class="analytics-table-wrap">
                        <table class="analytics-table">
                            <thead>
                                <tr>
                                    <th scope="col">Compte</th>
                                    <th scope="col">Signal</th>
                                    <th scope="col">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lockedUsers as $user)
                                    <tr>
                                        <td>
                                            <div class="analytics-strong">{{ $user->name }}</div>
                                            <div class="analytics-soft">{{ $user->email }}</div>
                                        </td>
                                        <td><span class="analytics-badge">Compte bloqué</span></td>
                                        <td>
                                            <div class="analytics-strong">{{ trans_choice('app.analytics.failed_attempts', $user->failed_login_attempts, ['count' => $user->failed_login_attempts]) }}</div>
                                            <div class="analytics-soft">Bloqué le {{ optional($user->locked_at)->format('d/m/Y H:i') ?: '-' }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach($suspiciousActivities as $entry)
                                    <tr>
                                        <td>
                                            <div class="analytics-strong">{{ optional($entry->user)->name ?: 'Utilisateur inconnu' }}</div>
                                            <div class="analytics-soft">{{ optional($entry->user)->email ?: '-' }}</div>
                                        </td>
                                        <td><span class="analytics-badge">Tentatives répétées</span></td>
                                        <td>
                                            <div class="analytics-strong">{{ $entry->failed_attempts }} échec(s)</div>
                                            <div class="analytics-soft">Dernière tentative le {{ $entry->last_attempt_at ? \Carbon\Carbon::parse($entry->last_attempt_at)->format('d/m/Y H:i') : '-' }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </section>

        <section class="panel analytics-panel">
            <div class="analytics-panel-top">
                <h3 class="analytics-panel-title">Dernières actions par utilisateur</h3>
                <div class="analytics-panel-meta">
                    {{ $userSearch !== '' ? 'Recherche : ' . $userSearch : '10 actions récentes maximum' }}
                </div>
            </div>

            <form method="GET" action="{{ route('manage.analytics.index') }}" class="analytics-inline-form">
                <input type="hidden" name="days" value="{{ $days }}">
                <div class="field">
                    <label>Rechercher un utilisateur</label>
                    <input
                        type="text"
                        name="user_search"
                        value="{{ $userSearch }}"
                        placeholder="Pseudo ou email"
                    >
                </div>
                <button class="btn" type="submit">Rechercher</button>
                @if($userSearch !== '')
                    <a class="btn secondary" href="{{ route('manage.analytics.index', ['days' => $days]) }}">Effacer</a>
                @endif
            </form>

            @if($recentActions->isEmpty())
                <div class="analytics-empty">
                    <p class="muted" style="margin:0;">Aucune action ne correspond à cette recherche.</p>
                </div>
            @else
                <div class="analytics-table-wrap">
                    <table class="analytics-table analytics-table--actions">
                        <thead>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Utilisateur</th>
                                <th scope="col">Action</th>
                                <th scope="col">Contexte</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($previousActionUserId = '__initial__')
                            @foreach($recentActions as $log)
                                @php($showActionUserEmail = $log->user_id !== $previousActionUserId)
                                <tr>
                                    <td><div class="analytics-strong">{{ optional($log->action_at)->format('d/m/Y H:i') }}</div></td>
                                    <td>
                                        <div class="analytics-strong">{{ optional($log->user)->name ?: 'Utilisateur supprimé' }}</div>
                                        @if($showActionUserEmail)
                                            <div class="analytics-soft">{{ optional($log->user)->email ?: '-' }}</div>
                                        @endif
                                    </td>
                                    @php
                                        $actionKey = (string) $log->action_user;
                                        $actionStyle = $actionKey === 'page_consulte' ? 'analytics-action--consultation' : '';
                                        if (strpos($actionKey, 'trash_force_delete') !== false || strpos($actionKey, 'trash_emptied') !== false || strpos($actionKey, 'delete') !== false || strpos($actionKey, 'suppression') !== false) {
                                            $actionStyle = 'analytics-action--suppression';
                                        } elseif (strpos($actionKey, 'modified') !== false || strpos($actionKey, 'changed') !== false || strpos($actionKey, 'update') !== false || strpos($actionKey, 'updated') !== false) {
                                            $actionStyle = 'analytics-action--modification';
                                        }
                                    @endphp
                                    <td><span class="analytics-badge {{ $actionStyle }}">{{ $log->action_label }}</span></td>
                                    <td><div class="analytics-soft" title="{{ $log->route_path ?: '-' }}">{{ $log->route_name ?: '-' }}</div></td>
                                </tr>
                                @php($previousActionUserId = $log->user_id)
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
