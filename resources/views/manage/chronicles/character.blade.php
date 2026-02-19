@extends('manage.layout')

@section('title', 'Gestion - Chroniques personnage')
@section('header', 'Chroniques')

@section('content')
    <style>
        .poster-page {
            --line-width: 78px;
            --line-half: 39px;
            --line-color: #d71920;
            --line-glow: rgba(215, 25, 32, .2);
        }
        .poster-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }
        .poster-title { margin: 0; }
        .poster-sub {
            margin: 4px 0 0;
            color: rgba(56,41,21,.72);
            font-size: .92rem;
        }
        .poster-timeline {
            position: relative;
            margin-top: 14px;
            padding: 8px 0;
        }
        .poster-timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: var(--line-width);
            transform: translateX(-50%);
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(255,255,255,.55) 0%, rgba(245,236,220,.45) 100%);
            box-shadow: 0 0 0 1px rgba(130, 18, 21, .1), 0 0 24px rgba(0,0,0,.05);
        }
        .poster-item {
            position: relative;
            width: 100%;
            display: flex;
            margin: 0 0 14px;
            min-height: 162px;
        }
        .poster-item.left { justify-content: flex-start; }
        .poster-item.right { justify-content: flex-end; }
        .poster-item.is-collapsed {
            min-height: 86px;
            margin-bottom: 8px;
        }
        .poster-item.is-collapsed .poster-body {
            display: none;
        }
        .poster-item.is-collapsed .poster-card {
            opacity: .93;
        }
        .poster-card {
            width: min(360px, calc(50% - var(--line-half) - 22px));
            position: relative;
            background: linear-gradient(180deg, #fffefb 0%, #f8efe2 100%);
            border: 2px solid rgba(51, 37, 20, .26);
            border-left: 6px solid var(--event-color, #8f6b3c);
            border-radius: 22px;
            padding: 11px 14px;
            box-shadow: 0 10px 20px rgba(0,0,0,.09);
        }
        .poster-switch {
            --switch-bg: #2f9f65;
            border: 1px solid rgba(25, 76, 46, .25);
            background: color-mix(in srgb, var(--switch-bg) 18%, #ffffff 82%);
            border-radius: 999px;
            padding: 3px 8px 3px 4px;
            min-width: 66px;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 7px;
            color: #1d4d32;
            font-size: .7rem;
            font-weight: 900;
            letter-spacing: .04em;
            cursor: pointer;
            transition: background .18s ease, color .18s ease, border-color .18s ease;
        }
        .poster-switch:hover {
            filter: brightness(1.02);
        }
        .poster-switch:focus-visible {
            outline: 2px solid #084f86;
            outline-offset: 2px;
        }
        .poster-switch-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid rgba(0,0,0,.2);
            box-shadow: 0 1px 3px rgba(0,0,0,.25);
            transition: transform .2s ease;
        }
        .poster-switch-text { line-height: 1; }
        .poster-item.is-collapsed .poster-switch {
            --switch-bg: #8f97a3;
            color: #3e4651;
            border-color: rgba(54, 63, 75, .25);
        }
        .poster-item.is-collapsed .poster-switch .poster-switch-dot {
            transform: translateX(36px);
        }
        .poster-year-marker {
            position: absolute;
            left: 50%;
            top: 8px;
            width: calc(var(--line-width) - 12px);
            height: calc(var(--year-span, 1) * 176px - 18px);
            min-height: 120px;
            transform: translateX(-50%);
            border-radius: 14px;
            background: var(--year-color, #d71920);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.22), 0 8px 18px rgba(0,0,0,.14);
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .poster-year-line {
            font-size: 2.2rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: .02em;
            color: rgba(255, 248, 241, .97);
            text-shadow: 0 1px 3px rgba(77, 7, 10, .6);
        }
        .poster-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 6px;
        }
        .poster-top-left {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }
        .poster-date { font-weight: 800; color: #5f4220; }
        .poster-badge {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            padding: 3px 7px;
            border-radius: 999px;
            border: 1px solid var(--event-color, #8f6b3c);
            background: rgba(255,255,255,.75);
            color: #2b2012;
        }
        .poster-event-title {
            margin: 0;
            font-size: 1.05rem;
            line-height: 1.2;
            color: #1f1a14;
        }
        .poster-photo {
            margin-top: 8px;
            width: 74px;
            height: 74px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid rgba(111,81,46,.28);
            box-shadow: 0 4px 10px rgba(0,0,0,.12);
        }
        .poster-desc {
            margin-top: 7px;
            color: #463827;
            font-size: .92rem;
            line-height: 1.35;
        }
        .poster-meta {
            margin-top: 7px;
            font-size: .86rem;
            color: #67594a;
        }
        .poster-related {
            margin-top: 8px;
            font-size: .8rem;
            color: #45311b;
        }
        .poster-open {
            margin-top: 8px;
            display: inline-block;
            font-weight: 700;
            font-size: .9rem;
        }
        .poster-actions {
            margin-top: 10px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }
        .poster-delete {
            border: 1px solid rgba(150, 40, 40, .35);
            background: rgba(255, 240, 240, .85);
            color: #7b1f1f;
            border-radius: 8px;
            padding: 5px 10px;
            font-weight: 700;
            cursor: pointer;
        }
        .poster-delete:hover {
            background: rgba(255, 225, 225, .95);
        }
        .poster-empty {
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px dashed rgba(117, 84, 48, .32);
            background: linear-gradient(180deg, rgba(255,255,255,.68) 0%, rgba(246,236,217,.55) 100%);
        }
        @media (max-width: 860px) {
            .poster-head { flex-direction: column; align-items: flex-start; }
            .poster-page {
                --line-width: 66px;
                --line-half: 33px;
            }
            .poster-timeline::before { left: 40px; transform: none; }
            .poster-item { justify-content: flex-end !important; }
            .poster-card { width: calc(100% - 102px); }
            .poster-year-marker { left: 40px; transform: translateX(-50%); }
            .poster-year-line { font-size: 1.8rem; }
        }
    </style>

    <section class="panel poster-page">
        <div class="poster-head">
            <div>
                <h3 class="poster-title">Frise - {{ $character->display_name }}</h3>
                <p class="poster-sub">Evenements alternes a gauche et a droite de la ligne centrale.</p>
            </div>
            <div class="stack">
                <a class="btn secondary" href="{{ route('manage.chronicles.index') }}">Retour</a>
                <a class="btn secondary" href="{{ route('manage.chronicles.global') }}">Global</a>
                <a class="btn" href="{{ route('manage.chronicles.create') }}">Nouvelle chronique</a>
            </div>
        </div>

        @php
            $typeLabels = [
                'chronicle' => 'Chronique',
                'character_event' => 'Personnage',
                'birth' => 'Naissance',
                'death' => 'Deces',
            ];
            $yearPalette = [
                '#7FA8A4', '#A8B79A', '#B39BC8', '#C7A58A', '#8FA7C9',
                '#C2B38D', '#9DB5AE', '#B7A7C8', '#B8B1A1', '#90A9A0',
            ];
            $yearColorMap = [];
            $yearIdx = 0;
            $lastYear = null;
            $yearCounts = $timelineEvents
                ->groupBy(function ($event) {
                    return $event['date'] ? $event['date']->format('Y') : '----';
                })
                ->map
                ->count();
        @endphp

        @if($timelineEvents->isEmpty())
            <p class="poster-empty muted">Aucun evenement a afficher.</p>
        @else
            <div class="poster-timeline">
                @foreach($timelineEvents as $event)
                    @php
                        $eventColor = $event['accent_color'] ?? null;
                        $year = $event['date'] ? $event['date']->format('Y') : '----';
                        if (!array_key_exists($year, $yearColorMap)) {
                            $yearColorMap[$year] = $year === '----'
                                ? '#7E7E7E'
                                : $yearPalette[$yearIdx++ % count($yearPalette)];
                        }
                        $yearColor = $yearColorMap[$year];
                        $showYear = $year !== $lastYear;
                        $lastYear = $year;
                        $side = ($loop->index % 2 === 0) ? 'left' : 'right';
                        $relatedPeople = collect($event['related_people'] ?? [])->filter()->values();
                        $cardId = 'timeline-card-character-' . $loop->index;
                    @endphp
                    <article class="poster-item {{ $side }}" style="{{ $eventColor ? '--event-color:' . $eventColor . ';' : '' }} --year-color: {{ $yearColor }};">
                        @if($showYear)
                            <span class="poster-year-marker" aria-hidden="true" style="--year-span: {{ (int) ($yearCounts[$year] ?? 1) }};">
                                <span class="poster-year-line">{{ $year }}</span>
                            </span>
                        @endif
                        <div class="poster-card" id="{{ $cardId }}">
                            <div class="poster-top">
                                <div class="poster-top-left">
                                    <span class="poster-date">{{ $event['date'] ? $event['date']->format('Y-m-d') : 'Date inconnue' }}</span>
                                    <span class="poster-badge">{{ $typeLabels[$event['type']] ?? 'Evenement' }}</span>
                                </div>
                                <button class="poster-switch" type="button" data-target="{{ $cardId }}" aria-expanded="true" aria-label="Masquer l'evenement" title="Masquer l'evenement">
                                    <span class="poster-switch-dot"></span>
                                    <span class="poster-switch-text">ON</span>
                                </button>
                            </div>

                            <h4 class="poster-event-title">{{ $event['title'] }}</h4>
                            <div class="poster-body">
                            @if(!empty($event['photo_path']))
                                <img class="poster-photo" src="{{ route('media.show', ['path' => $event['photo_path']]) }}" alt="Photo evenement">
                            @endif

                            @if(!empty($event['description']))
                                <div class="poster-desc">{{ $event['description'] }}</div>
                            @endif

                            @if(!empty($event['source_name']))
                                <div class="poster-meta">Source: {{ $event['source_name'] }}</div>
                            @endif

                            @if($relatedPeople->isNotEmpty())
                                <div class="poster-related">Personnes liees: {{ $relatedPeople->join(', ') }}</div>
                            @endif

                            <div class="poster-actions">
                                @if(!empty($event['link']))
                                    <a class="poster-open" href="{{ $event['link'] }}">Ouvrir</a>
                                @endif
                                @if(!empty($event['can_manage']) && !empty($event['edit_link']))
                                    <a class="poster-open" href="{{ $event['edit_link'] }}">Modifier</a>
                                @endif
                                @if(!empty($event['can_manage']) && !empty($event['delete_link']))
                                    <form method="POST" action="{{ $event['delete_link'] }}" onsubmit="return confirm('Supprimer cet evenement ?');" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="poster-delete" type="submit">Supprimer</button>
                                    </form>
                                @endif
                            </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
    <script>
        (function () {
            const buttons = document.querySelectorAll('.poster-switch[data-target]');
            if (!buttons.length) return;

            buttons.forEach(function (button) {
                const cardId = button.getAttribute('data-target');
                const article = button.closest('.poster-item');
                const card = article ? article.querySelector('#' + cardId) : null;
                if (!article || !card) return;

                button.addEventListener('click', function () {
                    const collapsed = !article.classList.contains('is-collapsed');
                    article.classList.toggle('is-collapsed', collapsed);
                    card.hidden = collapsed;
                    button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                    button.setAttribute('aria-label', collapsed ? 'Afficher l\'evenement' : 'Masquer l\'evenement');
                    button.setAttribute('title', collapsed ? 'Afficher l\'evenement' : 'Masquer l\'evenement');

                    const text = button.querySelector('.poster-switch-text');
                    if (text) {
                        text.textContent = collapsed ? 'OFF' : 'ON';
                    }
                });
            });
        })();
    </script>
@endsection
