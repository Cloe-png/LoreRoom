@extends('manage.layout')

@section('title', 'Gestion - Chroniques')
@section('header', 'Chroniques')

@section('content')
    <style>
        .chrono-wrap {
            overflow-x: auto;
            padding: 14px 6px 4px;
        }
        .chrono-track {
            position: relative;
            min-width: 980px;
            padding: 78px 26px 76px;
        }
        .chrono-axis {
            position: absolute;
            left: 24px;
            right: 40px;
            top: 50%;
            height: 4px;
            margin-top: -2px;
            border-radius: 999px;
            background: linear-gradient(90deg, #8e6a3b 0%, #be9356 70%, #d8b076 100%);
            box-shadow: 0 0 0 1px rgba(89,64,31,.16);
        }
        .chrono-axis::after {
            content: '';
            position: absolute;
            right: -18px;
            top: 50%;
            transform: translateY(-50%);
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-left: 18px solid #d8b076;
            filter: drop-shadow(0 0 1px rgba(0,0,0,.25));
        }
        .chrono-items {
            display: grid;
            grid-template-columns: repeat(var(--chrono-count, 1), minmax(180px, 1fr));
            gap: 0;
            align-items: stretch;
        }
        .chrono-item {
            position: relative;
            text-align: center;
            padding: 0 8px;
        }
        .chrono-item .dot {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 14px;
            height: 14px;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: #fff8ea;
            border: 3px solid #9f7640;
            box-shadow: 0 2px 5px rgba(0,0,0,.18);
            z-index: 2;
        }
        .chrono-item .stem {
            position: absolute;
            left: 50%;
            width: 2px;
            background: rgba(103,76,43,.55);
        }
        .chrono-item.up .stem {
            top: calc(50% - 48px);
            height: 40px;
            transform: translateX(-50%);
        }
        .chrono-item.down .stem {
            top: calc(50% + 8px);
            height: 40px;
            transform: translateX(-50%);
        }
        .chrono-card {
            display: inline-block;
            max-width: 180px;
            padding: 8px 10px;
            border-radius: 10px;
            border: 1px solid rgba(100,73,42,.28);
            background: rgba(255,255,255,.84);
            font-size: .85rem;
            line-height: 1.25;
            box-shadow: 0 4px 10px rgba(0,0,0,.07);
        }
        .chrono-item.up .chrono-card {
            margin-bottom: 66px;
        }
        .chrono-item.down .chrono-card {
            margin-top: 66px;
        }
        .chrono-date {
            display: block;
            font-weight: 700;
            color: #6b4b20;
            margin-bottom: 3px;
            font-size: .78rem;
        }
    </style>

    <div class="stack" style="justify-content: space-between;">
        <p class="muted">Entrees narratives et timeline de l univers.</p>
        <a class="btn" href="{{ route('manage.chronicles.create') }}">Nouvelle chronique</a>
    </div>

    @php
        $timelineItems = $chronicles->getCollection()
            ->sortBy(function ($chronicle) {
                return optional($chronicle->event_date)->format('Y-m-d') ?: '9999-12-31';
            })
            ->values();
    @endphp

    <section class="panel">
        <h3 style="margin-top:0;">Frise chronologique</h3>
        @if($timelineItems->isEmpty())
            <p class="muted">Aucun evenement a afficher.</p>
        @else
            <div class="chrono-wrap">
                <div class="chrono-track">
                    <div class="chrono-axis"></div>
                    <div class="chrono-items" style="--chrono-count: {{ max(1, $timelineItems->count()) }};">
                        @foreach($timelineItems as $chronicle)
                            @php
                                $isUp = ($loop->index % 2) === 0;
                                $date = optional($chronicle->event_date)->format('Y-m-d') ?: 'Date inconnue';
                            @endphp
                            <article class="chrono-item {{ $isUp ? 'up' : 'down' }}">
                                <span class="stem"></span>
                                <span class="dot"></span>
                                <div class="chrono-card">
                                    <span class="chrono-date">{{ $date }}</span>
                                    <strong>{{ $chronicle->title }}</strong>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </section>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Monde</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($chronicles as $chronicle)
                <tr>
                    <td>{{ $chronicle->title }}</td>
                    <td>{{ optional($chronicle->world)->name }}</td>
                    <td>{{ optional($chronicle->event_date)->format('Y-m-d') }}</td>
                    <td>{{ $chronicle->status }}</td>
                    <td class="stack">
                        <a class="btn secondary" href="{{ route('manage.chronicles.show', $chronicle) }}">Voir</a>
                        <a class="btn secondary" href="{{ route('manage.chronicles.edit', $chronicle) }}">Éditer</a>
                        <form class="inline" method="POST" action="{{ route('manage.chronicles.destroy', $chronicle) }}">
                            @csrf @method('DELETE')
                            <button class="btn danger" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Aucune chronique.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:10px;">{{ $chronicles->links() }}</div>
    </section>
@endsection
