@extends('manage.layout')

@section('title', 'Gestion - Personnage')
@section('header', $character->display_name)

@section('content')
    <style>
        .gallery-slide {
            display: none;
        }
        .gallery-slide.is-active {
            display: block;
        }
        .gallery-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 1px solid rgba(80,59,32,.45);
            background: rgba(255,255,255,.45);
            cursor: pointer;
        }
        .gallery-dot.is-active {
            background: #8f6a3a;
        }
    </style>
    <section class="panel">
        @php
            $children = $character->childrenFromFather->merge($character->childrenFromMother)->unique('id');
        @endphp

        <div class="grid-4">
            <div class="card">
                <strong>Portrait</strong>
                @if($character->image_path)
                    <p style="margin:10px 0 0;">
                        <img
                            src="{{ route('media.show', ['path' => $character->image_path]) }}"
                            alt="Portrait de {{ $character->display_name }}"
                            style="display:block; width:100%; max-width:220px; border-radius:10px; border:1px solid rgba(101,74,42,.35); box-shadow:0 10px 20px rgba(0,0,0,.18);"
                        >
                    </p>
                @else
                    <p class="muted">Aucune photo.</p>
                @endif
            </div>

            <div class="card">
                <strong>Identité</strong>
                <p><strong>Nom:</strong> {{ $character->display_name }}</p>
                <p><strong>Alias:</strong> {{ $character->aliases ?: '-' }}</p>
                <p><strong>Famille:</strong> {{ $character->family_name ?: ($character->last_name ?: '-') }}</p>
                <p><strong>Statut:</strong> {{ $character->status ?: '-' }}</p>
                <p><strong>Age:</strong> {{ $character->calculated_age !== null ? $character->calculated_age . ' ans' : '-' }}</p>
                <p><strong>Genre:</strong> {{ $character->gender ?: '-' }}</p>
                <p><strong>Habite à:</strong> {{ optional($character->world)->name ?: '-' }}</p>
                <p><strong>Rôle:</strong> {{ $character->role ?: '-' }}</p>
                <p><strong>Naissance:</strong> {{ optional($character->birth_date)->format('Y-m-d') ?: '-' }}</p>
                <p><strong>Mort:</strong> {{ optional($character->death_date)->format('Y-m-d') ?: '-' }}</p>
            </div>

            <div class="card" style="grid-column: span 2;">
                <strong>Famille et lieux</strong>
                <p><strong>Père:</strong> {{ optional($character->father)->display_name ?: 'Inconnu' }}</p>
                <p><strong>Mère:</strong> {{ optional($character->mother)->display_name ?: 'Inconnue' }}</p>
                <p><strong>A des enfants:</strong> {{ $character->has_children ? 'Oui' : 'Non' }}</p>
                <p><strong>Enfants liés:</strong> {{ $children->isEmpty() ? 'Aucun' : $children->pluck('display_name')->join(', ') }}</p>
                <p><strong>Lieu de naissance:</strong> {{ optional($character->birthPlace)->name ?: '-' }}</p>
                <p><strong>Résidence actuelle:</strong> {{ optional($character->residencePlace)->name ?: '-' }}</p>
            </div>
        </div>

        <div class="grid-4">
            <div class="card">
                <strong>Pouvoir</strong>
                <p><strong>Actif:</strong> {{ $character->has_power ? 'Oui' : 'Non' }}</p>
                <p><strong>Niveau:</strong> {{ $character->power_level ?: '-' }}/10</p>
                <p>{{ $character->power_description ?: '-' }}</p>
            </div>
            <div class="card" style="grid-column: span 3;">
                <strong>Objectifs et secret privé</strong>
                <p><strong>Court terme:</strong> {{ $character->short_term_goal ?: '-' }}</p>
                <p><strong>Long terme:</strong> {{ $character->long_term_goal ?: '-' }}</p>
                <p><strong>Secret privé:</strong>
                    @if($character->secrets_is_private && trim((string) $character->secrets) !== '')
                        (masqués)
                    @else
                        {{ $character->secrets ?: '-' }}
                    @endif
                </p>
            </div>
        </div>

        <div class="grid-4">
            <div class="card" style="grid-column: span 2;">
                <strong>Psychologie</strong>
                <p><strong>Qualités:</strong> {{ $character->qualities ?: '-' }}</p>
                <p><strong>Défauts:</strong> {{ $character->flaws ?: '-' }}</p>
                <p><strong>Voix / tics de langage:</strong> {{ $character->voice_tics ?: '-' }}</p>
            </div>
            <div class="card" style="grid-column: span 2;">
                <strong>Apparence</strong>
                <p><strong>Cheveux:</strong> {{ $character->hair_color ?: $character->hair_eyes ?: '-' }}</p>
                <p><strong>Yeux:</strong> {{ $character->eye_color ?: $character->hair_eyes ?: '-' }}</p>
                <p><strong>Marques:</strong> {{ $character->marks ?: '-' }}</p>
                <p><strong>Style:</strong> {{ $character->clothing_style ?: '-' }}</p>
            </div>
        </div>

        <div class="panel">
            <strong>Équipements / artefacts</strong>
            @if($character->items->isEmpty())
                <p class="muted">Aucun équipement.</p>
            @else
                <table>
                    <thead><tr><th>Nom</th><th>Rareté</th><th>Notes</th></tr></thead>
                    <tbody>
                        @foreach($character->items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->rarity ?: '-' }}</td>
                                <td>{{ $item->notes ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="panel">
            <strong>Métiers</strong>
            @if($character->jobs->isEmpty())
                <p class="muted">Aucun métier.</p>
            @else
                <table>
                    <thead><tr><th>Métier</th><th>Début</th><th>Fin</th><th>Notes</th></tr></thead>
                    <tbody>
                        @foreach($character->jobs as $job)
                            <tr>
                                <td>{{ $job->job_name }}</td>
                                <td>{{ $job->start_year ?: '-' }}</td>
                                <td>{{ $job->end_year ?: '-' }}</td>
                                <td>{{ $job->notes ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="panel">
            <strong>Galerie</strong>
            @if($character->galleryImages->isEmpty())
                <p class="muted">Aucune image de galerie.</p>
            @else
                <div id="character-gallery-carousel" style="position:relative; max-width:780px; margin-top:10px;">
                    @foreach($character->galleryImages as $img)
                        <div class="gallery-slide{{ $loop->first ? ' is-active' : '' }}">
                            <div style="height:430px; display:flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid rgba(101,74,42,.35); background:linear-gradient(180deg, rgba(34,27,20,.14), rgba(18,14,10,.22)); overflow:hidden;">
                                <img
                                    src="{{ route('media.show', ['path' => $img->image_path]) }}"
                                    alt="Galerie {{ $character->display_name }}"
                                    style="max-width:100%; max-height:100%; width:auto; height:auto; object-fit:contain; image-orientation:from-image;"
                                >
                            </div>
                            <div class="muted" style="margin-top:6px;">{{ $img->caption ?: 'Sans légende' }}</div>
                        </div>
                    @endforeach
                    @if($character->galleryImages->count() > 1)
                        <div id="gallery-dots" style="margin-top:8px; display:flex; gap:8px; justify-content:center;">
                            @foreach($character->galleryImages as $img)
                                <button type="button" class="gallery-dot{{ $loop->first ? ' is-active' : '' }}" data-slide-index="{{ $loop->index }}"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="panel">
            <strong>Résumé</strong>
            <p>{{ $character->summary ?: 'Aucun résumé.' }}</p>
        </div>

        <div class="stack">
            <a class="btn" href="{{ route('manage.characters.export-pdf', $character) }}">Exporter PDF</a>
            <a class="btn secondary" href="{{ route('manage.characters.edit', $character) }}">Éditer</a>
            <a class="btn secondary" href="{{ route('manage.characters.index') }}">Retour</a>
        </div>
    </section>

    @if($character->galleryImages->count() > 1)
        <script>
            (function () {
                const root = document.getElementById('character-gallery-carousel');
                if (!root) return;

                const slides = Array.from(root.querySelectorAll('.gallery-slide'));
                const dots = Array.from(document.querySelectorAll('#gallery-dots .gallery-dot'));
                let idx = 0;
                let timer = null;

                function render(nextIdx) {
                    idx = (nextIdx + slides.length) % slides.length;
                    slides.forEach((slide, i) => {
                        const active = i === idx;
                        slide.style.display = active ? 'block' : 'none';
                        slide.classList.toggle('is-active', active);
                    });
                    dots.forEach((dot, i) => {
                        const active = i === idx;
                        dot.classList.toggle('is-active', active);
                        dot.style.background = active ? '#8f6a3a' : 'rgba(255,255,255,.45)';
                    });
                }

                function start() {
                    stop();
                    timer = setInterval(() => render(idx + 1), 3500);
                }

                function stop() {
                    if (timer) clearInterval(timer);
                    timer = null;
                }

                dots.forEach((dot) => {
                    dot.addEventListener('click', function () {
                        render(Number(dot.dataset.slideIndex || 0));
                        start();
                    });
                });

                root.addEventListener('mouseenter', stop);
                root.addEventListener('mouseleave', start);

                render(0);
                start();
            })();
        </script>
    @endif
@endsection

