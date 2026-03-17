@extends('manage.layout')

@section('title', 'Gestion - Faction')
@section('header', $faction->name)

@section('content')
    <style>
        .info-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
        .tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid rgba(110, 78, 45, .35);
            background: rgba(255,255,255,.65);
            font-size: .82rem;
            color: #5f4422;
        }
        .list-card {
            border: 1px solid rgba(114, 84, 49, .35);
            border-radius: 10px;
            background: rgba(255,255,255,.22);
            padding: 10px;
        }
        .member-line {
            display: flex;
            gap: 8px;
            align-items: baseline;
            justify-content: space-between;
            border-bottom: 1px dashed rgba(114,84,49,.25);
            padding: 6px 0;
        }
        .member-line:last-child { border-bottom: 0; }
        .relation-line {
            display: grid;
            gap: 6px;
            padding: 8px 0;
            border-bottom: 1px dashed rgba(114,84,49,.25);
        }
        .relation-line:last-child { border-bottom: 0; }
        .dl-row { margin: 0 0 6px; }
    </style>

    <div class="stack" style="justify-content: space-between; align-items:center;">
        <div class="stack">
            @if($faction->logo_path)
                <img src="{{ route('media.show', ['path' => $faction->logo_path]) }}" alt="Logo {{ $faction->name }}" style="width:72px; height:72px; object-fit:cover; border-radius:12px; border:1px solid rgba(101,74,42,.28);">
            @endif
            <div>
                @if($faction->type)
                    <span class="tag">{{ $faction->type }}</span>
                @endif
                <div class="muted" style="margin-top:6px;">Monde: {{ optional($faction->world)->name ?: 'Monde actif' }}</div>
            </div>
        </div>
        <div class="stack">
            <a class="btn secondary" href="{{ route('manage.factions.edit', $faction) }}">Éditer</a>
            <form class="inline" method="POST" action="{{ route('manage.factions.destroy', $faction) }}">
                @csrf @method('DELETE')
                <button class="btn danger" type="submit">Supprimer</button>
            </form>
        </div>
    </div>

    <section class="panel">
        <div class="info-grid">
            <div>
                <strong>Membres</strong>
                <div class="metric">{{ $membersCount }}</div>
            </div>
            <div>
                <strong>Relations</strong>
                <div class="metric">{{ $relationsCount }}</div>
            </div>
        </div>
    </section>

    <section class="panel">
        <h3 style="margin-top:0;">Informations générales</h3>
        <p class="dl-row"><strong>Nom:</strong> {{ $faction->name }}</p>
        <p class="dl-row"><strong>Type:</strong> {{ $faction->type ?: '-' }}</p>
        <p class="dl-row"><strong>Statut:</strong> {{ $faction->status ?: '-' }}</p>
        <p class="dl-row"><strong>Date de création:</strong> {{ optional($faction->founded_at)->format('d/m/Y') ?: '-' }}</p>
        <p class="dl-row"><strong>Devise / slogan:</strong> {{ $faction->motto ?: '-' }}</p>
        <p class="dl-row"><strong>Description:</strong> {{ $faction->summary ?: '-' }}</p>
    </section>

    <section class="panel">
        <h3 style="margin-top:0;">Direction</h3>
        <p class="dl-row"><strong>Leader:</strong>
            @if($faction->leader)
                <a href="{{ route('manage.characters.show', $faction->leader) }}">{{ $faction->leader->display_name }}</a>
            @else
                -
            @endif
        </p>
        <p class="dl-row"><strong>Co-leader / bras droit:</strong>
            @if($faction->coLeader)
                <a href="{{ route('manage.characters.show', $faction->coLeader) }}">{{ $faction->coLeader->display_name }}</a>
            @else
                -
            @endif
        </p>
        <p class="dl-row"><strong>Fondateur:</strong>
            @if($faction->founder)
                <a href="{{ route('manage.characters.show', $faction->founder) }}">{{ $faction->founder->display_name }}</a>
            @else
                -
            @endif
        </p>
    </section>

    <section class="panel">
        <h3 style="margin-top:0;">Membres</h3>
        @if($faction->members->isEmpty())
            <p class="muted">Aucun membre enregistré.</p>
        @else
            <div class="list-card">
                @foreach($faction->members as $member)
                    <div class="member-line">
                        <div>
                            <strong>{{ $member->display_name }}</strong>
                            <span class="muted">· {{ $member->pivot->role ?: '-' }}</span>
                            <span class="muted">· grade: {{ $member->pivot->grade ?: '-' }}</span>
                            <span class="muted">· entrée: {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('d/m/Y') : '-' }}</span>
                            <span class="muted">· statut: {{ $member->pivot->status ?: '-' }}</span>
                        </div>
                        <a class="btn secondary" href="{{ route('manage.characters.show', $member) }}">Voir</a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="panel">
        <h3 style="margin-top:0;">Relations sortantes</h3>
        @if($faction->outgoingRelations->isEmpty())
            <p class="muted">Aucune relation sortante.</p>
        @else
            <div class="list-card">
                @foreach($faction->outgoingRelations as $relation)
                    <div class="relation-line">
                        <div>
                            <strong>{{ $relation->relatedFaction->name ?? 'Faction inconnue' }}</strong>
                            <span class="muted">· {{ $relation->relation_type }}</span>
                            @if($relation->is_bidirectional)
                                <span class="muted">· bidirectionnelle</span>
                            @endif
                        </div>
                        @if($relation->description)
                            <div class="muted">{{ $relation->description }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="panel">
        <h3 style="margin-top:0;">Relations entrantes</h3>
        @if($faction->incomingRelations->isEmpty())
            <p class="muted">Aucune relation entrante.</p>
        @else
            <div class="list-card">
                @foreach($faction->incomingRelations as $relation)
                    <div class="relation-line">
                        <div>
                            <strong>{{ $relation->faction->name ?? 'Faction inconnue' }}</strong>
                            <span class="muted">· {{ $relation->relation_type }}</span>
                            @if($relation->is_bidirectional)
                                <span class="muted">· bidirectionnelle</span>
                            @endif
                        </div>
                        @if($relation->description)
                            <div class="muted">{{ $relation->description }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    @if(mb_strtolower((string) $faction->type) === 'école')
        <section class="panel">
            <h3 style="margin-top:0;">Diplômes</h3>
            @if($faction->diplomas->isEmpty())
                <p class="muted">Aucun diplôme défini.</p>
            @else
                <div class="list-card">
                    @foreach($faction->diplomas as $diploma)
                        <div class="relation-line">
                            <div>
                                <strong>{{ $diploma->name }}</strong>
                                @if($diploma->level)
                                    <span class="muted">· {{ $diploma->level }}</span>
                                @endif
                            </div>
                            @if($diploma->description)
                                <div class="muted">{{ $diploma->description }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
@endsection
