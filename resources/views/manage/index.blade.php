@extends('manage.layout')

@section('title', 'Gestion - Accueil')
@section('header', 'Carnet de bord')

@section('content')
    <style>
        .journal {
            display: grid;
            gap: 14px;
        }

        .journal-cover {
            position: relative;
            border: 1px solid rgba(88, 61, 34, .35);
            border-radius: 12px;
            padding: 18px 16px;
            background:
                linear-gradient(180deg, rgba(214, 184, 140, .58), rgba(178, 142, 99, .48)),
                repeating-linear-gradient(90deg, rgba(77, 52, 27, .08) 0 2px, transparent 2px 11px),
                radial-gradient(780px 230px at 8% -12%, rgba(255, 239, 203, .3), transparent 66%);
            box-shadow: 0 12px 26px rgba(65, 42, 22, .2);
            overflow: hidden;
        }

        .journal-cover::after {
            content: "";
            position: absolute;
            right: -22px;
            top: -22px;
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(118, 84, 46, .24) 0%, rgba(118, 84, 46, 0) 72%);
            pointer-events: none;
        }

        .journal-title {
            margin: 0;
            color: #3a2614;
            font-family: "Cinzel", "Times New Roman", serif;
            letter-spacing: .04em;
            font-size: clamp(1.25rem, 2vw, 1.7rem);
        }

        .journal-subtitle {
            margin: 7px 0 0;
            color: #5d4326;
            max-width: 760px;
            font-size: .95rem;
            line-height: 1.4;
        }

        .journal-meta {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px dashed rgba(104, 73, 40, .45);
            border-radius: 999px;
            background: rgba(255, 246, 226, .52);
            color: #51371d;
            padding: 5px 11px;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .journal-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(12, 1fr);
        }

        .sheet {
            grid-column: span 12;
            border: 1px solid rgba(114, 84, 49, .3);
            border-radius: 10px;
            background:
                linear-gradient(180deg, rgba(248, 237, 215, .98), rgba(235, 218, 186, .97)),
                repeating-linear-gradient(0deg, transparent 0 29px, rgba(73, 120, 177, .18) 29px 30px);
            color: #322514;
            box-shadow: 0 8px 20px rgba(76, 53, 30, .14);
            padding: 14px;
            position: relative;
        }

        .sheet::before {
            content: "";
            position: absolute;
            left: 24px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(188, 72, 72, .35);
            opacity: .9;
        }

        .sheet-content {
            padding-left: 24px;
        }

        .sheet-title {
            margin: 0 0 8px;
            color: #4a3117;
            font-family: "Segoe Print", "Comic Sans MS", cursive;
            font-size: 1.12rem;
        }

        .stats {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        }

        .sticky {
            border: 1px solid rgba(109, 78, 45, .25);
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 8px 15px rgba(50, 34, 17, .11);
            transform: rotate(var(--tilt));
            min-height: 92px;
        }

        .sticky:nth-child(1) { --tilt: -1deg; background: linear-gradient(180deg, #f8dfa3, #ebc271); }
        .sticky:nth-child(2) { --tilt: .8deg; background: linear-gradient(180deg, #c6e2d5, #9dc9b7); }
        .sticky:nth-child(3) { --tilt: -0.7deg; background: linear-gradient(180deg, #c6d7ef, #a5bee2); }
        .sticky:nth-child(4) { --tilt: .9deg; background: linear-gradient(180deg, #e8c8c8, #dca6a6); }

        .sticky-label {
            color: #5f4628;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .sticky-value {
            margin-top: 6px;
            color: #2e2113;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1.95rem;
            line-height: 1;
        }

        .columns {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .entry-list {
            margin: 6px 0 0;
            padding-left: 18px;
        }

        .entry-list li + li {
            margin-top: 5px;
        }

        .ledger {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .ledger th,
        .ledger td {
            border-bottom: 1px dashed rgba(102, 73, 41, .34);
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
            color: #3f2d1a;
        }

        .ledger th {
            color: #674620;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-size: .78rem;
            font-family: "Cinzel", "Times New Roman", serif;
        }

        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            padding-left: 24px;
        }

        .empty-note {
            margin: 6px 0 0;
            color: #6d5234;
            font-size: .92rem;
            font-style: italic;
        }

        @media (max-width: 980px) {
            .columns {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="journal">
        <section class="journal-cover">
            <h2 class="journal-title">Carnet des événements</h2>
            <p class="journal-subtitle">Tableau de bord narratif pour suivre les anniversaires, la frise du jour et les prochaines entrées de ton univers.</p>
            <div class="journal-meta">{{ ucfirst($today->locale('fr')->translatedFormat('l j F Y')) }}</div>
            <div class="quick-actions">
                <a class="btn" href="{{ route('manage.chronicles.create') }}">Ajouter un événement</a>
                <a class="btn secondary" href="{{ route('manage.characters.create') }}">Ajouter un personnage</a>
            </div>
        </section>

        <section class="sheet">
            <div class="sheet-content">
                <h3 class="sheet-title">Repères rapides</h3>
                <div class="stats">
                    <article class="sticky">
                        <div class="sticky-label">Mondes</div>
                        <div class="sticky-value">{{ $worldsCount }}</div>
                    </article>
                    <article class="sticky">
                        <div class="sticky-label">Personnages</div>
                        <div class="sticky-value">{{ $charactersCount }}</div>
                    </article>
                    <article class="sticky">
                        <div class="sticky-label">Chroniques</div>
                        <div class="sticky-value">{{ $chroniclesCount }}</div>
                    </article>
                    <article class="sticky">
                        <div class="sticky-label">Cartes</div>
                        <div class="sticky-value">{{ $mapsCount }}</div>
                    </article>
                </div>
            </div>
        </section>

        <section class="sheet">
            <div class="sheet-content">
                <h3 class="sheet-title">Entrées du jour</h3>
                <div class="columns">
                    <div>
                        <strong>Anniversaires</strong>
                        @if($todayBirthdays->isEmpty())
                            <p class="empty-note">Aucun anniversaire aujourd'hui.</p>
                        @else
                            <ul class="entry-list">
                                @foreach($todayBirthdays as $character)
                                    <li>
                                        {{ $character->display_name }}
                                        @if($character->world)
                                            <span class="muted">- {{ $character->world->name }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div>
                        <strong>Frise chrono (aujourd'hui)</strong>
                        @if($todayChronicles->isEmpty())
                            <p class="empty-note">Aucun événement prévu aujourd'hui.</p>
                        @else
                            <ul class="entry-list">
                                @foreach($todayChronicles as $chronicle)
                                    <li>
                                        {{ $chronicle->title }}
                                        @if($chronicle->world)
                                            <span class="muted">- {{ $chronicle->world->name }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="sheet">
            <div class="sheet-content">
                <h3 class="sheet-title">À venir (14 jours)</h3>
                @if($upcomingChronicles->isEmpty())
                    <p class="empty-note">Aucun événement à venir sur les 14 prochains jours.</p>
                @else
                    <table class="ledger">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Titre</th>
                                <th>Monde</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingChronicles as $chronicle)
                                <tr>
                                    <td>{{ optional($chronicle->event_date)->locale('fr')->translatedFormat('d/m/Y') }}</td>
                                    <td>{{ $chronicle->title }}</td>
                                    <td>{{ optional($chronicle->world)->name ?: '-' }}</td>
                                    <td>{{ $chronicle->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </section>
    </div>
@endsection
