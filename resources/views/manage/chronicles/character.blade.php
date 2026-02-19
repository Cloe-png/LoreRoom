@extends('manage.layout')

@section('title', 'Gestion - Chroniques personnage')
@section('header', 'Chroniques')

@section('content')
    <style>
        .character-surface {
            --tone-text: #2d2112;
        }
        .character-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }
        .character-title {
            margin: 0;
            color: var(--tone-text);
        }
        .character-sub {
            margin: 4px 0 0;
            color: rgba(56,41,21,.72);
            font-size: .92rem;
        }
        .timeline-wrap {
            margin-top: 18px;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 10px;
        }
        .timeline-track {
            position: relative;
            min-width: 960px;
            padding: 86px 20px;
        }
        .timeline-axis {
            position: absolute;
            left: 20px;
            right: 34px;
            top: 50%;
            height: 6px;
            transform: translateY(-50%);
            border-radius: 999px;
            background: linear-gradient(90deg, #866038 0%, #c7975c 58%, #e3be89 100%);
            box-shadow: 0 0 0 1px rgba(89,64,31,.16);
        }
        .timeline-axis::after {
            content: '';
            position: absolute;
            right: -22px;
            top: 50%;
            transform: translateY(-50%);
            border-top: 12px solid transparent;
            border-bottom: 12px solid transparent;
            border-left: 22px solid #d6ab72;
            filter: drop-shadow(1px 1px 0 rgba(89,64,31,.32));
        }
        .timeline-items {
            display: grid;
            grid-template-columns: repeat(var(--event-count, 1), minmax(210px, 1fr));
            gap: 0;
            align-items: stretch;
        }
        .timeline-item {
            position: relative;
            text-align: center;
            padding: 0 10px;
        }
        .timeline-item .dot {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 14px;
            height: 14px;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: #fff8ea;
            border: 3px solid var(--event-color, #9f7640);
            box-shadow: 0 2px 5px rgba(0,0,0,.18);
            z-index: 2;
        }
        .timeline-item .stem {
            position: absolute;
            left: 50%;
            width: 2px;
            background: rgba(103,76,43,.55);
        }
        .timeline-item.up .stem {
            top: calc(50% - 54px);
            height: 46px;
            transform: translateX(-50%);
        }
        .timeline-item.down .stem {
            top: calc(50% + 8px);
            height: 46px;
            transform: translateX(-50%);
        }
        .timeline-event {
            display: inline-block;
            width: min(240px, 100%);
            border: 1px solid rgba(113, 81, 46, .22);
            background: linear-gradient(180deg, rgba(255,255,255,.92) 0%, rgba(251,245,235,.9) 100%);
            border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 8px 18px rgba(0,0,0,.06);
            text-align: left;
            border-top: 4px solid var(--event-color, rgba(113, 81, 46, .28));
        }
        .timeline-item.up .timeline-event {
            margin-bottom: 74px;
        }
        .timeline-item.down .timeline-event {
            margin-top: 74px;
        }
        .timeline-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 7px;
        }
        .timeline-date {
            font-weight: 700;
            color: #5f4220;
        }
        .timeline-title {
            font-weight: 700;
            color: #1f1a14;
            font-size: 1.08rem;
        }
        .badge {
            display: inline-block;
            font-size: .7rem;
            padding: 3px 9px;
            border-radius: 999px;
            border: 1px solid transparent;
            text-transform: uppercase;
            letter-spacing: .02em;
        }
        .badge.character_event {
            background: color-mix(in srgb, var(--event-color, #9fbfda) 16%, #ffffff 84%);
            border-color: color-mix(in srgb, var(--event-color, #9fbfda) 70%, #ffffff 30%);
            color: #294c66;
        }
        .badge.birth {
            background: color-mix(in srgb, var(--event-color, #94c39a) 16%, #ffffff 84%);
            border-color: color-mix(in srgb, var(--event-color, #94c39a) 70%, #ffffff 30%);
            color: #2a5830;
        }
        .badge.death {
            background: color-mix(in srgb, var(--event-color, #d49a9a) 16%, #ffffff 84%);
            border-color: color-mix(in srgb, var(--event-color, #d49a9a) 70%, #ffffff 30%);
            color: #6a2d2d;
        }
        .timeline-meta {
            margin-top: 6px;
            color: #67594a;
            font-size: .9rem;
        }
        .timeline-link {
            font-size: .84rem;
            color: #5a2f8d;
            font-weight: 700;
        }
        .timeline-empty {
            margin-top: 18px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px dashed rgba(117, 84, 48, .32);
            background: linear-gradient(180deg, rgba(255,255,255,.68) 0%, rgba(246,236,217,.55) 100%);
        }
        @media (max-width: 640px) {
            .character-head {
                flex-direction: column;
                align-items: flex-start;
            }
            .timeline-track {
                min-width: 760px;
                padding: 78px 14px;
            }
        }
    </style>

    @php
        $characterColor = null;
        if (!empty($character->preferred_color)) {
            $normalized = trim($character->preferred_color);
            if ($normalized !== '' && $normalized[0] !== '#') {
                $normalized = '#' . $normalized;
            }
            if (preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $normalized)) {
                $characterColor = strtoupper($normalized);
            }
        }
    @endphp

    <section class="panel character-surface" style="{{ $characterColor ? '--event-color:' . $characterColor . ';' : '' }}">
        <div class="character-head">
            <div>
                <h3 class="character-title">Frise - {{ $character->display_name }}</h3>
                <p class="character-sub">Chronologie dediee au personnage.</p>
            </div>
            <div class="stack">
                <a class="btn secondary" href="{{ url()->previous() }}">Retour</a>
                <a class="btn secondary" href="{{ route('manage.chronicles.index', ['mode' => 'global']) }}">Global</a>
                <a class="btn" href="{{ route('manage.chronicles.create') }}">Nouvelle chronique</a>
            </div>
        </div>

        @php
            $timelineItems = $timelineEvents->values();

            $typeLabels = [
                'character_event' => 'Personnage',
                'birth' => 'Naissance',
                'death' => 'Deces',
            ];
        @endphp

        @if($timelineItems->isEmpty())
            <p class="timeline-empty muted">Aucun evenement a afficher.</p>
        @else
            <div class="timeline-wrap">
                <div class="timeline-track">
                    <div class="timeline-axis"></div>
                    <div class="timeline-items" style="--event-count: {{ max(1, $timelineItems->count()) }};">
                        @foreach($timelineItems as $event)
                            @php
                                $isUp = ($loop->index % 2) === 0;
                                $eventColor = $event['accent_color'] ?? null;
                            @endphp
                            <article class="timeline-item {{ $isUp ? 'up' : 'down' }}" style="{{ $eventColor ? '--event-color:' . $eventColor . ';' : '' }}">
                                <span class="stem"></span>
                                <span class="dot"></span>
                                <div class="timeline-event">
                                    <div class="timeline-head">
                                        <span class="timeline-date">{{ $event['date'] ? $event['date']->format('Y-m-d') : 'Date inconnue' }}</span>
                                        <span class="badge {{ $event['type'] }}">{{ $typeLabels[$event['type']] ?? 'Evenement' }}</span>
                                    </div>
                                    <div class="timeline-title">{{ $event['title'] }}</div>
                                    @if(!empty($event['description']))
                                        <div style="margin-top:6px;">{{ $event['description'] }}</div>
                                    @endif
                                    @if(!empty($event['source_name']))
                                        <div class="timeline-meta">Source: {{ $event['source_name'] }}</div>
                                    @endif
                                    @if(!empty($event['link']))
                                        <div style="margin-top:8px;">
                                            <a class="timeline-link" href="{{ $event['link'] }}">Ouvrir</a>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection
