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
            overflow: hidden;
        }
        .poster-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(220px 60px at 96% 10%, rgba(255,255,255,.55), transparent 70%),
                repeating-linear-gradient(135deg, rgba(132,103,66,.03) 0px, rgba(132,103,66,.03) 2px, transparent 2px, transparent 8px);
            pointer-events: none;
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
            top: 0;
            width: calc(var(--line-width) - 12px);
            height: calc(var(--year-span, 1) * 176px);
            min-height: 120px;
            transform: translateX(-50%);
            border-radius: 0;
            background: var(--year-color, #d71920);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.22), 0 8px 18px rgba(0,0,0,.14);
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .poster-year-marker.year-first {
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }
        .poster-year-marker.year-last {
            border-bottom-left-radius: 14px;
            border-bottom-right-radius: 14px;
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
        .poster-body {
            margin-top: 8px;
        }
        .poster-shell {
            border: 1px solid rgba(78,58,33,.22);
            border-radius: 16px;
            overflow: hidden;
            background: rgba(255,255,255,.45);
        }
        .poster-hero {
            position: relative;
            height: 86px;
            background:
                linear-gradient(180deg, rgba(37,31,22,.38), rgba(37,31,22,.2)),
                var(--hero-image, linear-gradient(120deg, #d7c2a2, #b99261));
            background-size: cover;
            background-position: center;
            border-bottom: 1px solid rgba(70,50,29,.3);
        }
        .poster-hero-label {
            position: absolute;
            left: 10px;
            bottom: 8px;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #f8efe0;
            background: rgba(21,17,13,.46);
            border: 1px solid rgba(255,255,255,.22);
        }
        .poster-middle {
            display: grid;
            grid-template-columns: minmax(0,1fr) 88px;
            gap: 10px;
            padding: 11px 11px 8px;
            border-bottom: 1px solid rgba(70,50,29,.18);
        }
        .poster-desc {
            margin-top: 7px;
            color: #463827;
            font-size: .92rem;
            line-height: 1.35;
        }
        .poster-portrait {
            width: 88px;
            height: 88px;
            border-radius: 22px;
            object-fit: cover;
            border: 2px solid rgba(84,63,38,.35);
            box-shadow: 0 5px 12px rgba(0,0,0,.16);
            background: #efe5d5;
        }
        .poster-portrait-fallback {
            width: 88px;
            height: 88px;
            border-radius: 22px;
            border: 2px solid rgba(84,63,38,.35);
            background: linear-gradient(180deg, #f2eadf, #e4d6c1);
            color: #5f4422;
            font-size: 1.6rem;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 12px rgba(0,0,0,.12);
        }
        .poster-foot {
            position: relative;
            padding: 8px 11px 10px;
            background: rgba(255,255,255,.72);
        }
        .poster-foot::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(255,255,255,.74), rgba(255,255,255,.84)),
                var(--hero-image, linear-gradient(120deg, #ddc8aa, #c09a69));
            background-size: cover;
            background-position: center bottom;
            opacity: .35;
            pointer-events: none;
        }
        .poster-foot > * {
            position: relative;
            z-index: 1;
        }
        .poster-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 6px;
        }
        .poster-stat {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: .72rem;
            border: 1px solid rgba(80,61,36,.24);
            background: rgba(255,255,255,.72);
            color: #4a3721;
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
                <p class="poster-sub">Événements alternés à gauche et à droite de la ligne centrale.</p>
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
                'death' => 'Décès',
            ];
            $yearPalette = [
                '#7FA8A4', '#A8B79A', '#B39BC8', '#C7A58A', '#8FA7C9',
                '#C2B38D', '#9DB5AE', '#B7A7C8', '#B8B1A1', '#90A9A0',
            ];
            $yearColorMap = [];
            $yearIdx = 0;
            $lastYear = null;
            $yearOrder = $timelineEvents
                ->map(function ($event) {
                    return $event['date'] ? $event['date']->format('Y') : '----';
                })
                ->unique()
                ->values();
            $yearCounts = $timelineEvents
                ->groupBy(function ($event) {
                    return $event['date'] ? $event['date']->format('Y') : '----';
                })
                ->map
                ->count();
        @endphp

        @if($timelineEvents->isEmpty())
            <p class="poster-empty muted">Aucun événement à afficher.</p>
        @else
            <div class="poster-timeline">
                @foreach($timelineEvents as $event)
                    @php
                        $eventColor = $event['accent_color'] ?? null;
                        $year = $event['date'] ? $event['date']->format('Y') : '----';
                        $dateLabel = $event['date'] ? $event['date']->format('d/m/Y') : 'Date inconnue';
                        if (!empty($event['end_date'])) {
                            $dateLabel .= ' -> ' . $event['end_date']->format('d/m/Y');
                        }
                        if (!array_key_exists($year, $yearColorMap)) {
                            $yearColorMap[$year] = $year === '----'
                                ? '#7E7E7E'
                                : $yearPalette[$yearIdx++ % count($yearPalette)];
                        }
                        $yearColor = $yearColorMap[$year];
                        $showYear = $year !== $lastYear;
                        $lastYear = $year;
                        $isFirstYear = $year === $yearOrder->first();
                        $isLastYear = $year === $yearOrder->last();
                        $side = ($loop->index % 2 === 0) ? 'left' : 'right';
                        $cardId = 'timeline-card-character-' . $loop->index;
                        $photoUrl = !empty($event['photo_path']) ? route('media.show', ['path' => $event['photo_path']]) : null;
                        $portraitInitial = mb_strtoupper(mb_substr((string) ($event['source_name'] ?? $event['title'] ?? 'E'), 0, 1));
                    @endphp
                    <article class="poster-item {{ $side }}" style="{{ $eventColor ? '--event-color:' . $eventColor . ';' : '' }} --year-color: {{ $yearColor }};">
                        @if($showYear)
                            <span class="poster-year-marker {{ $isFirstYear ? 'year-first' : '' }} {{ $isLastYear ? 'year-last' : '' }}" data-year-marker="1" aria-hidden="true" style="--year-span: {{ (int) ($yearCounts[$year] ?? 1) }};">
                                <span class="poster-year-line">{{ $year }}</span>
                            </span>
                        @endif
                        <div class="poster-card" id="{{ $cardId }}">
                            <div class="poster-top">
                                <div class="poster-top-left">
                                    <span class="poster-date">{{ $dateLabel }}</span>
                                    <span class="poster-badge">{{ $typeLabels[$event['type']] ?? 'Événement' }}</span>
                                </div>
                                <button class="poster-switch" type="button" data-target="{{ $cardId }}" aria-expanded="true" aria-label="Masquer l'événement" title="Masquer l'événement">
                                    <span class="poster-switch-dot"></span>
                                    <span class="poster-switch-text">ON</span>
                                </button>
                            </div>

                            <div class="poster-body">
                            <div class="poster-shell" style="{{ $photoUrl ? "--hero-image:url('{$photoUrl}');" : '' }}">
                                <div class="poster-hero">
                                    <span class="poster-hero-label">{{ $typeLabels[$event['type']] ?? 'Événement' }}</span>
                                </div>

                                <div class="poster-middle">
                                    <div>
                                        <h4 class="poster-event-title">{{ $event['title'] }}</h4>
                                        @if(!empty($event['location']))
                                            <div class="poster-meta">Lieu: {{ $event['location'] }}</div>
                                        @endif
                                        @if(!empty($event['description']))
                                            <div class="poster-desc">{{ $event['description'] }}</div>
                                        @else
                                            <div class="poster-desc muted">Aucune description.</div>
                                        @endif
                                    </div>
                                    <div>
                                        @if($photoUrl)
                                            <img class="poster-portrait" src="{{ $photoUrl }}" alt="Photo événement">
                                        @else
                                            <div class="poster-portrait-fallback">{{ $portraitInitial }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="poster-foot">
                                    <div class="poster-actions">
                                        @if(!empty($event['can_manage']) && !empty($event['edit_link']))
                                            <a class="poster-open" href="{{ $event['edit_link'] }}">Modifier</a>
                                        @endif
                                        @if(!empty($event['can_manage']) && !empty($event['delete_link']))
                                            <form method="POST" action="{{ $event['delete_link'] }}" onsubmit="return confirm('Supprimer cet événement ?');" style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="poster-delete" type="submit">Supprimer</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
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
            const timeline = document.querySelector('.poster-timeline');
            const relayoutYearMarkers = () => {
                if (!timeline) return;
                const markers = Array.from(timeline.querySelectorAll('[data-year-marker="1"]'));
                if (!markers.length) return;

                markers.forEach((marker, idx) => {
                    const article = marker.closest('.poster-item');
                    if (!article) return;
                    const top = article.offsetTop;
                    const nextArticle = idx < markers.length - 1 ? markers[idx + 1].closest('.poster-item') : null;
                    const nextTop = nextArticle ? nextArticle.offsetTop : timeline.scrollHeight;
                    const h = Math.max(120, nextTop - top);
                    marker.style.height = `${h}px`;
                });
            };

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
                    button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                    button.setAttribute('aria-label', collapsed ? 'Afficher l\'événement' : 'Masquer l\'événement');
                    button.setAttribute('title', collapsed ? 'Afficher l\'événement' : 'Masquer l\'événement');

                    const text = button.querySelector('.poster-switch-text');
                    if (text) {
                        text.textContent = collapsed ? 'OFF' : 'ON';
                    }
                    relayoutYearMarkers();
                });
            });

            relayoutYearMarkers();
            window.addEventListener('resize', relayoutYearMarkers);
        })();
    </script>
@endsection


