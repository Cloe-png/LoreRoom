@extends('manage.layout')

@section('title', 'Gestion - Espèces')
@section('header', 'Espèces / races')

@section('content')
    <div class="stack" style="justify-content: space-between;">
        <p class="muted">Catalogue des espèces / races du monde.</p>
        <a class="btn" href="{{ route('manage.species.create') }}">Nouvelle espèce</a>
    </div>

    <section class="panel" style="margin-top:8px;">
        <form method="GET" action="{{ route('manage.species.index') }}" class="stack" style="align-items:flex-end;">
            <div class="field" style="margin:0; min-width:min(420px, 100%);">
                <label>Recherche</label>
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Nom, caractéristiques, capacités...">
            </div>
            <button class="btn" type="submit">Rechercher</button>
            @if(!empty($q))
                <a class="btn secondary" href="{{ route('manage.species.index') }}">Effacer</a>
            @endif
        </form>
    </section>

    <section class="panel">
        <style>
            .species-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
                gap: 14px;
            }
            .species-card {
                border: 1px solid rgba(101,74,42,.28);
                border-radius: 12px;
                background: rgba(255,255,255,.55);
                padding: 12px;
                box-shadow: 0 8px 18px rgba(70, 45, 19, .12);
                display: grid;
                gap: 8px;
            }
            .species-name {
                font-family: "Cinzel","Times New Roman",serif;
                color: #4f381b;
                font-size: 1.05rem;
                margin: 0;
            }
            .species-meta {
                color: #6b4b2a;
                font-size: .85rem;
            }
        </style>

        @if($species->isEmpty())
            <p class="muted" style="margin:0;">Aucune espèce enregistrée.</p>
        @else
            <div class="species-grid">
                @foreach($species as $item)
                    <article class="species-card">
                        <h3 class="species-name">{{ $item->name }}</h3>
                        <div class="species-meta">Monde: {{ optional($item->world)->name ?: 'Monde actif' }}</div>
                        <div class="stack">
                            <a class="btn secondary" href="{{ route('manage.species.show', $item) }}">Voir</a>
                            <a class="btn secondary" href="{{ route('manage.species.edit', $item) }}">Éditer</a>
                            <form class="inline" method="POST" action="{{ route('manage.species.destroy', $item) }}">
                                @csrf @method('DELETE')
                                <button class="btn danger" type="submit">Supprimer</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <div class="stack" style="margin-top:10px; justify-content:space-between;">
            <p class="muted" style="margin:0;">
                Page {{ $species->currentPage() }} / {{ max(1, $species->lastPage()) }}
            </p>
            <div class="stack">
                @if($species->onFirstPage())
                    <span class="btn secondary" style="opacity:.55; pointer-events:none;">Précédent</span>
                @else
                    <a class="btn secondary" href="{{ $species->previousPageUrl() }}">Précédent</a>
                @endif

                @for($page = 1; $page <= $species->lastPage(); $page++)
                    @if($page === $species->currentPage())
                        <span class="btn" style="pointer-events:none;">{{ $page }}</span>
                    @else
                        <a class="btn secondary" href="{{ $species->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                @if($species->hasMorePages())
                    <a class="btn secondary" href="{{ $species->nextPageUrl() }}">Suivant</a>
                @else
                    <span class="btn secondary" style="opacity:.55; pointer-events:none;">Suivant</span>
                @endif
            </div>
        </div>
    </section>
@endsection
