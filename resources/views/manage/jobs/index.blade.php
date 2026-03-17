@extends('manage.layout')

@section('title', 'Gestion - Métiers')
@section('header', 'Métiers')

@section('content')
    <div class="stack" style="justify-content: space-between;">
        <p class="muted">Référentiel des métiers (par défaut + personnalisés).</p>
        <a class="btn" href="{{ route('manage.jobs.create') }}">Nouveau métier</a>
    </div>

    <section class="panel" style="margin-top:8px;">
        <form method="GET" action="{{ route('manage.jobs.index') }}" class="stack" style="align-items:flex-end;">
            <div class="field" style="margin:0; min-width:min(420px, 100%);">
                <label>Recherche</label>
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Nom, description...">
            </div>
            <button class="btn" type="submit">Rechercher</button>
            @if(!empty($q))
                <a class="btn secondary" href="{{ route('manage.jobs.index') }}">Effacer</a>
            @endif
        </form>
    </section>

    <section class="panel">
        <style>
            .jobs-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
                gap: 14px;
            }
            .job-card {
                border: 1px solid rgba(101,74,42,.28);
                border-radius: 12px;
                background: rgba(255,255,255,.55);
                padding: 12px;
                box-shadow: 0 8px 18px rgba(70, 45, 19, .12);
                display: grid;
                gap: 8px;
            }
            .job-name {
                font-family: "Cinzel","Times New Roman",serif;
                color: #4f381b;
                font-size: 1.05rem;
                margin: 0;
            }
            .job-meta { color:#6b4b2a; font-size:.85rem; }
        </style>

        @if($jobs->isEmpty())
            <p class="muted" style="margin:0;">Aucun métier.</p>
        @else
            <div class="jobs-grid">
                @foreach($jobs as $job)
                    <article class="job-card">
                        <h3 class="job-name">{{ $job->name }}</h3>
                        <div class="job-meta">
                            {{ $job->is_default ? 'Par défaut' : 'Personnalisé' }}
                            · Monde: {{ optional($job->world)->name ?: 'Global' }}
                        </div>
                        @if($job->description)
                            <div class="muted">{{ \Illuminate\Support\Str::limit($job->description, 120) }}</div>
                        @endif
                        <div class="stack">
                            <a class="btn secondary" href="{{ route('manage.jobs.show', $job) }}">Voir</a>
                            @if(!$job->is_default)
                                <a class="btn secondary" href="{{ route('manage.jobs.edit', $job) }}">Éditer</a>
                                <form class="inline" method="POST" action="{{ route('manage.jobs.destroy', $job) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn danger" type="submit">Supprimer</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <div class="stack" style="margin-top:10px; justify-content:space-between;">
            <p class="muted" style="margin:0;">
                Page {{ $jobs->currentPage() }} / {{ max(1, $jobs->lastPage()) }}
            </p>
            <div class="stack">
                @if($jobs->onFirstPage())
                    <span class="btn secondary" style="opacity:.55; pointer-events:none;">Précédent</span>
                @else
                    <a class="btn secondary" href="{{ $jobs->previousPageUrl() }}">Précédent</a>
                @endif

                @for($page = 1; $page <= $jobs->lastPage(); $page++)
                    @if($page === $jobs->currentPage())
                        <span class="btn" style="pointer-events:none;">{{ $page }}</span>
                    @else
                        <a class="btn secondary" href="{{ $jobs->url($page) }}">{{ $page }}</a>
                    @endif
                @endfor

                @if($jobs->hasMorePages())
                    <a class="btn secondary" href="{{ $jobs->nextPageUrl() }}">Suivant</a>
                @else
                    <span class="btn secondary" style="opacity:.55; pointer-events:none;">Suivant</span>
                @endif
            </div>
        </div>
    </section>
@endsection
