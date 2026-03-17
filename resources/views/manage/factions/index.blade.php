@extends('manage.layout')

@section('title', 'Gestion - Factions')
@section('header', 'Factions / Organisations')

@section('content')
    <div class="stack" style="justify-content: space-between;">
        <p class="muted">Crée et organise les factions, guildes et organisations de ton monde.</p>
        <a class="btn" href="{{ route('manage.factions.create') }}">Nouvelle faction</a>
    </div>

    <section class="panel" style="margin-top:10px;">
        <style>
            .factions-table td { vertical-align: middle; }
            .faction-name { font-family: "Cinzel","Times New Roman",serif; color:#4f381b; }
            .faction-meta { color:#6b4b2a; font-size:.88rem; }
        </style>

        @if($factions->isEmpty())
            <p class="muted" style="margin:0;">Aucune faction enregistrée pour le moment.</p>
        @else
            <table class="factions-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Membres</th>
                        <th>Relations</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($factions as $faction)
                        @php
                            $relationsCount = (int) $faction->outgoing_relations_count + (int) $faction->incoming_relations_count;
                        @endphp
                        <tr>
                            <td>
                                <div class="faction-name">{{ $faction->name }}</div>
                                <div class="faction-meta">Monde: {{ optional($faction->world)->name ?: 'Monde actif' }}</div>
                            </td>
                            <td>{{ $faction->type ?: '-' }}</td>
                            <td>{{ $faction->members_count }}</td>
                            <td>{{ $relationsCount }}</td>
                            <td>
                                <div class="stack">
                                    <a class="btn secondary" href="{{ route('manage.factions.show', $faction) }}">Voir</a>
                                    <a class="btn secondary" href="{{ route('manage.factions.edit', $faction) }}">Éditer</a>
                                    <form class="inline" method="POST" action="{{ route('manage.factions.destroy', $faction) }}">
                                        @csrf @method('DELETE')
                                        <button class="btn danger" type="submit">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="stack" style="margin-top:10px; justify-content:space-between;">
                <p class="muted" style="margin:0;">
                    Page {{ $factions->currentPage() }} / {{ max(1, $factions->lastPage()) }}
                </p>
                <div class="stack">
                    @if($factions->onFirstPage())
                        <span class="btn secondary" style="opacity:.55; pointer-events:none;">Précédent</span>
                    @else
                        <a class="btn secondary" href="{{ $factions->previousPageUrl() }}">Précédent</a>
                    @endif

                    @for($page = 1; $page <= $factions->lastPage(); $page++)
                        @if($page === $factions->currentPage())
                            <span class="btn" style="pointer-events:none;">{{ $page }}</span>
                        @else
                            <a class="btn secondary" href="{{ $factions->url($page) }}">{{ $page }}</a>
                        @endif
                    @endfor

                    @if($factions->hasMorePages())
                        <a class="btn secondary" href="{{ $factions->nextPageUrl() }}">Suivant</a>
                    @else
                        <span class="btn secondary" style="opacity:.55; pointer-events:none;">Suivant</span>
                    @endif
                </div>
            </div>
        @endif
    </section>
@endsection
