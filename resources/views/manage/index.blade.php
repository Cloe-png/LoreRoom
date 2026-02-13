@extends('manage.layout')

@section('title', 'Gestion - Accueil')
@section('header', 'Accueil')

@section('content')
    <section class="panel" style="margin-top:0;">
        <div class="stack" style="justify-content: space-between; align-items: flex-start;">
            <div>
                <h2 style="margin:0 0 6px; font-family:'Segoe Print','Comic Sans MS',cursive;">Journal des événements</h2>
                <p class="muted" style="max-width:780px;">Vue rapide des anniversaires personnages et des événements issus de la frise chronologique.</p>
            </div>
            <div class="stack">
                <a class="btn" href="{{ route('manage.chronicles.create') }}">Ajouter un événement</a>
                <a class="btn secondary" href="{{ route('manage.characters.create') }}">Ajouter un personnage</a>
            </div>
        </div>
    </section>

    <div class="grid-4">
        <div class="card">
            <div>Mondes</div>
            <div class="metric">{{ $worldsCount }}</div>
        </div>
        <div class="card">
            <div>Personnages</div>
            <div class="metric">{{ $charactersCount }}</div>
        </div>
        <div class="card">
            <div>Chroniques</div>
            <div class="metric">{{ $chroniclesCount }}</div>
        </div>
        <div class="card">
            <div>Cartes</div>
            <div class="metric">{{ $mapsCount }}</div>
        </div>
    </div>

    <section class="panel">
        <h2 style="margin:0 0 10px; font-family:'Segoe Print','Comic Sans MS',cursive;">Aujourd'hui ({{ $today->format('Y-m-d') }})</h2>
        <div class="grid-4">
            <div class="card" style="grid-column: span 2;">
                <strong>Anniversaires</strong>
                @if($todayBirthdays->isEmpty())
                    <p class="muted">Aucun anniversaire aujourd'hui.</p>
                @else
                    <ul style="margin:8px 0 0; padding-left:16px;">
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

            <div class="card" style="grid-column: span 2;">
                <strong>Frise chrono (aujourd'hui)</strong>
                @if($todayChronicles->isEmpty())
                    <p class="muted">Aucun événement prévu aujourd'hui.</p>
                @else
                    <ul style="margin:8px 0 0; padding-left:16px;">
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
    </section>

    <section class="panel">
        <div class="stack" style="justify-content: space-between;">
            <h2 style="margin:0; font-family:'Segoe Print','Comic Sans MS',cursive;">À venir (14 jours)</h2>
            <a class="btn secondary" href="{{ route('manage.chronicles.index') }}">Ouvrir la frise</a>
        </div>

        @if($upcomingChronicles->isEmpty())
            <p class="muted">Aucun événement à venir sur les 14 prochains jours.</p>
        @else
            <table>
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
                            <td>{{ optional($chronicle->event_date)->format('Y-m-d') }}</td>
                            <td>{{ $chronicle->title }}</td>
                            <td>{{ optional($chronicle->world)->name }}</td>
                            <td>{{ $chronicle->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>
@endsection
