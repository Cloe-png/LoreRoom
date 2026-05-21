@extends('manage.layout')

@section('title', 'Gestion - Monde')
@section('header', $world->name)

@section('content')
    @php
        $charactersPreview = $world->characters->take(5);
        $placesPreview = $world->places->take(4);
        $chroniclesPreview = $world->chronicles->take(4);
        $worldSummary = trim((string) ($world->summary ?? ''));
    @endphp

    <style>
        .world-view {
            display: grid;
            gap: 16px;
        }
        .world-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(114, 84, 49, .28);
            border-radius: 24px;
            padding: 24px;
            background:
                radial-gradient(560px 260px at 0% 0%, rgba(246, 207, 137, .34), transparent 72%),
                radial-gradient(460px 220px at 100% 0%, rgba(111, 165, 206, .24), transparent 72%),
                linear-gradient(135deg, rgba(255,255,255,.74), rgba(238, 222, 190, .82));
            box-shadow: 0 22px 42px rgba(69, 44, 18, .16);
        }
        .world-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(120deg, transparent 0%, rgba(255,255,255,.34) 46%, transparent 58%);
            transform: translateX(-60%);
            animation: worldHeroSweep 7s ease-in-out infinite;
            pointer-events: none;
        }
        .world-hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 20px;
            grid-template-columns: minmax(0, 1.1fr) minmax(300px, .9fr);
            align-items: center;
        }
        .world-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 34px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.68);
            border: 1px solid rgba(102, 74, 43, .18);
            color: #6b4b2b;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .1em;
        }
        .world-title {
            margin: 12px 0 0;
            color: #3b2613;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: clamp(1.8rem, 4vw, 3rem);
            line-height: 1;
        }
        .world-summary {
            margin: 14px 0 0;
            max-width: 780px;
            color: #61482a;
            font-size: 1rem;
            line-height: 1.65;
        }
        .world-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }
        .world-constellation {
            position: relative;
            min-height: 330px;
            border-radius: 20px;
            border: 1px solid rgba(88, 65, 36, .18);
            background:
                radial-gradient(circle at center, rgba(251, 244, 229, .96), rgba(229, 212, 180, .84)),
                linear-gradient(180deg, rgba(255,255,255,.46), rgba(255,255,255,.14));
            overflow: hidden;
        }
        .world-constellation svg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }
        .world-node {
            position: absolute;
            width: 108px;
            min-height: 108px;
            margin: -54px 0 0 -54px;
            padding: 12px 10px;
            border-radius: 999px;
            display: grid;
            place-items: center;
            text-align: center;
            color: #3d2815;
            background: rgba(255,255,255,.78);
            border: 1px solid rgba(88, 64, 38, .2);
            box-shadow: 0 12px 28px rgba(75, 51, 24, .15);
            backdrop-filter: blur(4px);
            animation: worldNodeFloat 6s ease-in-out infinite;
        }
        .world-node strong {
            display: block;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1.5rem;
            color: #4a3017;
        }
        .world-node span {
            display: block;
            margin-top: 4px;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #74593a;
        }
        .world-node.is-primary {
            width: 132px;
            min-height: 132px;
            margin: -66px 0 0 -66px;
            background: linear-gradient(180deg, rgba(255,248,236,.96), rgba(242, 223, 190, .92));
            box-shadow: 0 18px 34px rgba(79, 54, 27, .2);
        }
        .world-node.is-primary strong {
            font-size: 1.85rem;
        }
        .world-node.is-2 { animation-delay: -1.4s; }
        .world-node.is-3 { animation-delay: -2.2s; }
        .world-node.is-4 { animation-delay: -3.4s; }
        .world-node.is-5 { animation-delay: -4.1s; }
        .world-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(12, minmax(0, 1fr));
        }
        .world-card {
            grid-column: span 12;
            border: 1px solid rgba(114, 84, 49, .24);
            border-radius: 18px;
            background: rgba(255,255,255,.34);
            box-shadow: 0 12px 24px rgba(69, 44, 18, .08);
            overflow: hidden;
        }
        .world-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 16px 18px 10px;
        }
        .world-card-head h2 {
            margin: 0;
            color: #462d15;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1.12rem;
            letter-spacing: .03em;
        }
        .world-card-body {
            padding: 0 18px 18px;
        }
        .world-stats {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
        .world-stat {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            padding: 16px 14px;
            border: 1px solid rgba(106, 77, 42, .16);
            background: linear-gradient(180deg, rgba(255,255,255,.8), rgba(244, 231, 205, .7));
        }
        .world-stat::before {
            content: "";
            position: absolute;
            inset: auto -16px -24px auto;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(226, 190, 127, .14);
        }
        .world-stat strong {
            display: block;
            color: #4d3217;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: clamp(1.4rem, 2.6vw, 2rem);
            line-height: 1;
        }
        .world-stat span {
            display: block;
            margin-top: 8px;
            color: #73573a;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        .world-columns {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .world-list {
            display: grid;
            gap: 10px;
        }
        .world-list-item {
            display: grid;
            gap: 6px;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid rgba(114, 84, 49, .18);
            background: linear-gradient(180deg, rgba(255,255,255,.76), rgba(249, 242, 227, .76));
        }
        .world-list-item strong {
            color: #412a14;
        }
        .world-list-item p {
            margin: 0;
            color: #6f5537;
            line-height: 1.45;
        }
        .world-empty {
            margin: 0;
            color: #71563a;
            font-style: italic;
        }
        @keyframes worldHeroSweep {
            0%, 16% { transform: translateX(-70%); }
            35%, 100% { transform: translateX(130%); }
        }
        @keyframes worldNodeFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        @media (max-width: 980px) {
            .world-hero {
                padding: 18px;
            }
            .world-hero-grid,
            .world-columns {
                grid-template-columns: 1fr;
            }
            .world-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 640px) {
            .world-constellation {
                min-height: 280px;
            }
            .world-node {
                width: 88px;
                min-height: 88px;
                margin: -44px 0 0 -44px;
                padding: 10px 8px;
            }
            .world-node strong {
                font-size: 1.25rem;
            }
            .world-node.is-primary {
                width: 108px;
                min-height: 108px;
                margin: -54px 0 0 -54px;
            }
            .world-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="world-view">
        <section class="world-hero">
            <div class="world-hero-grid">
                <div>
                    <h2 class="world-title">{{ $world->name }}</h2>
                    <p class="world-summary">
                        @if($worldSummary !== '')
                            {{ $worldSummary }}
                        @else
                            Un espace de travail pour faire exister ce monde, relier ses figures, et transformer ses événements en mémoire vivante.
                        @endif
                    </p>
                    <div class="world-actions">
                        <a class="btn secondary" href="{{ route('manage.worlds.edit', $world) }}">Éditer</a>
                        <a class="btn secondary" href="{{ route('manage.worlds.index') }}">Retour</a>
                        <a class="btn" href="{{ route('manage.characters.create') }}">Ajouter un personnage</a>
                    </div>
                </div>
                <div class="world-constellation" aria-hidden="true">
                    <svg viewBox="0 0 100 100" preserveAspectRatio="none">
                        <line x1="50" y1="50" x2="24" y2="24" stroke="rgba(116,81,45,.28)" stroke-width="0.7" />
                        <line x1="50" y1="50" x2="76" y2="22" stroke="rgba(77,112,156,.28)" stroke-width="0.7" />
                        <line x1="50" y1="50" x2="78" y2="72" stroke="rgba(76,122,105,.28)" stroke-width="0.7" />
                        <line x1="50" y1="50" x2="24" y2="76" stroke="rgba(177,102,91,.22)" stroke-width="0.7" />
                        <line x1="24" y1="24" x2="76" y2="22" stroke="rgba(116,81,45,.16)" stroke-width="0.55" stroke-dasharray="2.5 2.5" />
                        <line x1="24" y1="76" x2="78" y2="72" stroke="rgba(116,81,45,.16)" stroke-width="0.55" stroke-dasharray="2.5 2.5" />
                    </svg>
                    <div class="world-node is-primary is-1" style="left:50%; top:50%;">
                        <div>
                            <strong>{{ $world->characters->count() + $world->places->count() + $world->chronicles->count() }}</strong>
                            <span>entrées vivantes</span>
                        </div>
                    </div>
                    <div class="world-node is-2" style="left:24%; top:24%;">
                        <div>
                            <strong>{{ $world->characters->count() }}</strong>
                            <span>personnages</span>
                        </div>
                    </div>
                    <div class="world-node is-3" style="left:76%; top:22%;">
                        <div>
                            <strong>{{ $world->chronicles->count() }}</strong>
                            <span>chroniques</span>
                        </div>
                    </div>
                    <div class="world-node is-4" style="left:24%; top:76%;">
                        <div>
                            <strong>{{ $world->places->count() }}</strong>
                            <span>lieux</span>
                        </div>
                    </div>
                    <div class="world-node is-5" style="left:78%; top:72%;">
                        <div>
                            <strong>{{ $factionsCount }}</strong>
                            <span>factions</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="world-card">
            <div class="world-card-head">
                <h2>Signal du monde</h2>
                <span class="muted">Lecture rapide du niveau de densité narrative</span>
            </div>
            <div class="world-card-body">
                <div class="world-stats">
                    <article class="world-stat">
                        <strong>{{ $world->characters->count() }}</strong>
                        <span>Personnages</span>
                    </article>
                    <article class="world-stat">
                        <strong>{{ $world->places->count() }}</strong>
                        <span>Lieux</span>
                    </article>
                    <article class="world-stat">
                        <strong>{{ $world->chronicles->count() }}</strong>
                        <span>Chroniques</span>
                    </article>
                    <article class="world-stat">
                        <strong>{{ $factionsCount }}</strong>
                        <span>Factions</span>
                    </article>
                    <article class="world-stat">
                        <strong>{{ $relationsCount }}</strong>
                        <span>Relations</span>
                    </article>
                </div>
            </div>
        </section>

        <section class="world-grid">
            <article class="world-card" style="grid-column: span 7;">
                <div class="world-card-head">
                    <h2>Visages clés</h2>
                    <a class="btn secondary" href="{{ route('manage.characters.index') }}">Voir tout</a>
                </div>
                <div class="world-card-body">
                    @if($charactersPreview->isEmpty())
                        <p class="world-empty">Aucun personnage pour l’instant.</p>
                    @else
                        <div class="world-list">
                            @foreach($charactersPreview as $character)
                                <article class="world-list-item">
                                    <strong>{{ $character->display_name }}</strong>
                                    <p>
                                        {{ $character->role ?: 'Rôle non défini' }}
                                        @if($character->birth_date)
                                            · né(e) le {{ $character->birth_date->format('d/m/Y') }}
                                        @endif
                                    </p>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </article>

            <article class="world-card" style="grid-column: span 5;">
                <div class="world-card-head">
                    <h2>Points d’ancrage</h2>
                    <a class="btn secondary" href="{{ route('manage.places.index') }}">Explorer</a>
                </div>
                <div class="world-card-body">
                    @if($placesPreview->isEmpty())
                        <p class="world-empty">Aucun lieu enregistré.</p>
                    @else
                        <div class="world-list">
                            @foreach($placesPreview as $place)
                                <article class="world-list-item">
                                    <strong>{{ $place->name }}</strong>
                                    <p>{{ $place->type ?: 'Type libre' }}@if($place->region) · {{ $place->region }} @endif</p>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </article>

            <article class="world-card" style="grid-column: span 12;">
                <div class="world-card-head">
                    <h2>Chroniques qui donnent le ton</h2>
                    <a class="btn secondary" href="{{ route('manage.chronicles.index') }}">Ouvrir la frise</a>
                </div>
                <div class="world-card-body">
                    @if($chroniclesPreview->isEmpty())
                        <p class="world-empty">Aucune chronique disponible.</p>
                    @else
                        <div class="world-columns">
                            @foreach($chroniclesPreview as $chronicle)
                                <article class="world-list-item">
                                    <strong>{{ $chronicle->title }}</strong>
                                    <p>
                                        @if($chronicle->event_date)
                                            {{ $chronicle->event_date->format('d/m/Y') }}
                                        @else
                                            Date libre
                                        @endif
                                        @if($chronicle->summary)
                                            · {{ \Illuminate\Support\Str::limit($chronicle->summary, 120) }}
                                        @endif
                                    </p>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </article>
        </section>
    </div>
@endsection
