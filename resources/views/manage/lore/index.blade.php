@extends('manage.layout')

@section('title', 'Gestion - Lore')
@section('header', 'Système de lore')

@section('content')
    <div class="stack" style="justify-content: space-between;">
        <p class="muted">Wiki interne du monde : religions, cultures, races, technologies, règles du monde…</p>
        <a class="btn" href="{{ route('manage.lore.create') }}">Nouvelle entrée</a>
    </div>

    <section class="panel" style="margin-top:8px;">
        <form method="GET" action="{{ route('manage.lore.index') }}" class="stack" style="align-items:flex-end;">
            <div class="field" style="margin:0; min-width:min(360px, 100%);">
                <label>Recherche</label>
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Titre, contenu, tags...">
            </div>
            <div class="field" style="margin:0; min-width:220px;">
                <label>Catégorie</label>
                <select name="category">
                    <option value="">Toutes</option>
                    @foreach($categoryOptions as $opt)
                        <option value="{{ $opt }}" {{ $category === $opt ? 'selected' : '' }}>{{ ucfirst($opt) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn" type="submit">Filtrer</button>
            @if(!empty($q) || !empty($category))
                <a class="btn secondary" href="{{ route('manage.lore.index') }}">Effacer</a>
            @endif
        </form>
    </section>

    <section class="panel">
        <style>
            .lore-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
                gap: 14px;
            }
            .lore-card {
                border: 1px solid rgba(101,74,42,.28);
                border-radius: 12px;
                background: rgba(255,255,255,.55);
                padding: 12px;
                box-shadow: 0 8px 18px rgba(70, 45, 19, .12);
                display: grid;
                gap: 8px;
            }
            .lore-title {
                font-family: "Cinzel","Times New Roman",serif;
                color: #4f381b;
                font-size: 1.05rem;
                margin: 0;
            }
            .lore-meta {
                color: #6b4b2a;
                font-size: .85rem;
            }
            .lore-excerpt {
                color: #2f2418;
                font-size: .9rem;
            }
        </style>

        @if($entries->isEmpty())
            <p class="muted" style="margin:0;">Aucune entrée de lore.</p>
        @else
            <div class="lore-grid">
                @foreach($entries as $entry)
                    <article class="lore-card">
                        <h3 class="lore-title">{{ $entry->title }}</h3>
                        <div class="lore-meta">
                            {{ $entry->category ? ucfirst($entry->category) : 'Sans catégorie' }}
                            · Monde: {{ optional($entry->world)->name ?: 'Monde actif' }}
                        </div>
                        <div class="lore-excerpt">
                            {{ \Illuminate\Support\Str::limit(strip_tags((string) $entry->content), 140) }}
                        </div>
                        @if($entry->tags)
                            <div class="muted">Tags: {{ $entry->tags }}</div>
                        @endif
                        <div class="stack">
                            <a class="btn secondary" href="{{ route('manage.lore.show', $entry) }}">Voir</a>
                            <a class="btn secondary" href="{{ route('manage.lore.edit', $entry) }}">Éditer</a>
                            <form class="inline" method="POST" action="{{ route('manage.lore.destroy', $entry) }}">
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
                Page {{ $entries->currentPage() }} / {{ max(1, $entries->lastPage()) }}
            </p>
            <div class="stack">
                @if($entries->onFirstPage())
                    <span class="btn secondary" style="opacity:.55; pointer-events:none;">Précédent</span>
                @else
                    <a class="btn secondary" href="{{ $entries->previousPageUrl() }}">Précédent</a>
                @endif

                @for($page = 1; $page <= $entries->lastPage(); $page++)
                    @if($page === $entries->currentPage())
                        <span class="btn" style="pointer-events:none;">{{ $page }}</span>
                    @else
                        <a class="btn secondary" href="{{ $entries->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                @if($entries->hasMorePages())
                    <a class="btn secondary" href="{{ $entries->nextPageUrl() }}">Suivant</a>
                @else
                    <span class="btn secondary" style="opacity:.55; pointer-events:none;">Suivant</span>
                @endif
            </div>
        </div>
    </section>
@endsection
