@extends('manage.layout')

@section('title', 'Gestion - Personnages')
@section('header', 'Personnages')

@section('content')
    <div class="stack" style="justify-content: space-between;">
        <p class="muted">Catalogue des personnages et de leur role narratif.</p>
        <a class="btn" href="{{ route('manage.characters.create') }}">Nouveau personnage</a>
    </div>

    <section class="panel" style="margin-top:8px;">
        <form method="GET" action="{{ route('manage.characters.index') }}" class="stack" style="align-items:flex-end;">
            <div class="field" style="margin:0; min-width:min(420px, 100%);">
                <label>Recherche personnage</label>
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Nom, prenom, role, monde, qualites...">
            </div>
            <button class="btn" type="submit">Rechercher</button>
            @if(!empty($q))
                <a class="btn secondary" href="{{ route('manage.characters.index') }}">Effacer</a>
            @endif
        </form>
    </section>

    <section class="panel">
        <style>
            .wanted-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 14px;
            }
            .wanted-card {
                border: 2px solid rgba(96, 66, 33, .55);
                border-radius: 10px;
                background:
                    linear-gradient(180deg, rgba(250, 239, 215, .96), rgba(235, 214, 173, .95)),
                    repeating-linear-gradient(0deg, rgba(90,62,30,.05) 0 2px, rgba(0,0,0,0) 2px 7px);
                box-shadow: 0 8px 20px rgba(61, 37, 16, .2);
                padding: 10px;
                color: #3a2a17;
            }
            .wanted-head {
                text-align: center;
                font-family: "Cinzel","Times New Roman",serif;
                font-size: .86rem;
                letter-spacing: .2em;
                color: #5f421f;
                text-transform: uppercase;
                margin-bottom: 8px;
                border-bottom: 1px dashed rgba(97,66,34,.4);
                padding-bottom: 6px;
            }
            .wanted-photo {
                width: 100%;
                aspect-ratio: 4 / 5;
                border-radius: 8px;
                border: 2px solid rgba(97,66,34,.45);
                background: rgba(255,255,255,.45);
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .wanted-photo img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .wanted-fallback {
                font-family: "Cinzel","Times New Roman",serif;
                font-size: .95rem;
                color: #7a623f;
                letter-spacing: .04em;
                text-transform: uppercase;
            }
            .wanted-meta {
                margin-top: 9px;
                text-align: center;
            }
            .wanted-last {
                font-family: "Cinzel","Times New Roman",serif;
                font-size: 1.05rem;
                color: #4f381b;
                letter-spacing: .04em;
                text-transform: uppercase;
                line-height: 1.2;
            }
            .wanted-first {
                font-family: "Garamond","Georgia",serif;
                font-size: 1rem;
                color: #5f4423;
                line-height: 1.25;
            }
            .wanted-actions {
                margin-top: 10px;
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                justify-content: center;
            }
            .wanted-empty {
                text-align: center;
                padding: 18px 10px;
            }
        </style>

        @if(!empty($q))
            <p class="muted" style="margin-top:0;">Resultats pour: <strong>{{ $q }}</strong></p>
        @endif

        @if($characters->isEmpty())
            <div class="wanted-empty muted">Aucun personnage.</div>
        @else
            <div class="wanted-grid">
                @foreach($characters as $character)
                    @php
                        $firstName = trim((string) ($character->first_name ?: ''));
                        $lastName = trim((string) ($character->last_name ?: ''));
                        $fallbackName = trim((string) ($character->name ?: ''));
                        if ($firstName === '' && $lastName === '' && $fallbackName !== '') {
                            $parts = preg_split('/\s+/', $fallbackName);
                            $firstName = (string) array_shift($parts);
                            $lastName = trim(implode(' ', $parts));
                        }
                    @endphp
                    <article class="wanted-card">
                        <div class="wanted-photo">
                            @if(!empty($character->image_path))
                                <img src="{{ route('media.show', ['path' => $character->image_path]) }}" alt="Portrait {{ $character->display_name }}">
                            @else
                                <div class="wanted-fallback">Sans photo</div>
                            @endif
                        </div>
                        <div class="wanted-meta">
                            <div class="wanted-last">{{ $lastName !== '' ? $lastName : '-' }}</div>
                            <div class="wanted-first">{{ $firstName !== '' ? $firstName : ($fallbackName !== '' ? $fallbackName : '-') }}</div>
                        </div>
                        <div class="wanted-actions">
                            <a class="btn secondary" href="{{ route('manage.characters.show', $character) }}">Voir</a>
                            <a class="btn secondary" href="{{ route('manage.characters.edit', $character) }}">Éditer</a>
                            <form class="inline" method="POST" action="{{ route('manage.characters.destroy', $character) }}">
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
                Page {{ $characters->currentPage() }} / {{ max(1, $characters->lastPage()) }}
            </p>
            <div class="stack">
                @if($characters->onFirstPage())
                    <span class="btn secondary" style="opacity:.55; pointer-events:none;">Précédent</span>
                @else
                    <a class="btn secondary" href="{{ $characters->previousPageUrl() }}">Précédent</a>
                @endif

                @for($page = 1; $page <= $characters->lastPage(); $page++)
                    @if($page === $characters->currentPage())
                        <span class="btn" style="pointer-events:none;">{{ $page }}</span>
                    @else
                        <a class="btn secondary" href="{{ $characters->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                @if($characters->hasMorePages())
                    <a class="btn secondary" href="{{ $characters->nextPageUrl() }}">Suivant</a>
                @else
                    <span class="btn secondary" style="opacity:.55; pointer-events:none;">Suivant</span>
                @endif
            </div>
        </div>
    </section>
@endsection
