@extends('manage.layout')

@section('title', 'Gestion - Chroniques globales')
@section('header', 'Chroniques')

@section('content')
    <style>
        .poster-page {
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
            width: 12px;
            transform: translateX(-50%);
            border-radius: 999px;
            background: linear-gradient(180deg, #ff4046 0%, var(--line-color) 20%, #c2141a 100%);
            box-shadow: 0 0 0 1px rgba(130, 18, 21, .35), 0 0 24px var(--line-glow);
        }
        .poster-item {
            position: relative;
            width: 100%;
            display: flex;
            margin: 0 0 14px;
        }
        .poster-item.left { justify-content: flex-start; }
        .poster-item.right { justify-content: flex-end; }
        .poster-card {
            width: min(360px, calc(50% - 34px));
            position: relative;
            background: linear-gradient(180deg, #fffcf8 0%, #f8efe2 100%);
            border: 1px solid rgba(113, 81, 46, .22);
            border-left: 6px solid var(--event-color, #8f6b3c);
            border-radius: 12px;
            padding: 10px 12px;
            box-shadow: 0 8px 18px rgba(0,0,0,.08);
        }
        .poster-dot {
            position: absolute;
            left: 50%;
            top: 18px;
            width: 14px;
            height: 14px;
            transform: translateX(-50%);
            border-radius: 50%;
            background: #fff;
            border: 4px solid var(--event-color, #8f6b3c);
            box-shadow: 0 0 0 4px rgba(255,255,255,.8), 0 4px 8px rgba(0,0,0,.18);
            z-index: 2;
        }
        .poster-year-line {
            position: absolute;
            left: 50%;
            top: 42px;
            transform: translateX(-50%);
            font-size: 1.15rem;
            font-weight: 900;
            line-height: 1;
            padding: 2px 6px;
            border-radius: 6px;
            background: rgba(255, 255, 255, .82);
            color: color-mix(in srgb, var(--event-color, #8f6b3c) 88%, #ffffff 12%);
            box-shadow: 0 1px 4px rgba(0,0,0,.12);
            z-index: 2;
        }
        .poster-top {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
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
            .poster-timeline::before { left: 26px; transform: none; }
            .poster-item { justify-content: flex-end !important; }
            .poster-card { width: calc(100% - 58px); }
            .poster-dot { left: 32px; transform: none; }
            .poster-year-line { left: 32px; transform: translateX(-50%); }
        }
    </style>

    <section class="panel poster-page">
        <div class="poster-head">
            <div>
                <h3 class="poster-title">Frise globale</h3>
                <p class="poster-sub">Evenements alternes a gauche et a droite de la ligne centrale.</p>
            </div>
            <div class="stack">
                <a class="btn secondary" href="{{ route('manage.chronicles.index') }}">Retour</a>
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
        @endphp

        @if($timelineEvents->isEmpty())
            <p class="poster-empty muted">Aucun evenement a afficher.</p>
        @else
            <div class="poster-timeline">
                @foreach($timelineEvents as $event)
                    @php
                        $eventColor = $event['accent_color'] ?? null;
                        $year = $event['date'] ? $event['date']->format('Y') : '----';
                        $side = ($loop->index % 2 === 0) ? 'left' : 'right';
                        $relatedPeople = collect($event['related_people'] ?? [])->filter()->values();
                    @endphp
                    <article class="poster-item {{ $side }}" style="{{ $eventColor ? '--event-color:' . $eventColor . ';' : '' }}">
                        <span class="poster-dot"></span>
                        <span class="poster-year-line">{{ $year }}</span>
                        <div class="poster-card">
                            <div class="poster-top">
                                <span class="poster-date">{{ $event['date'] ? $event['date']->format('Y-m-d') : 'Date inconnue' }}</span>
                                <span class="poster-badge">{{ $typeLabels[$event['type']] ?? 'Evenement' }}</span>
                            </div>

                            <h4 class="poster-event-title">{{ $event['title'] }}</h4>

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
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection
