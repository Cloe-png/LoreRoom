@extends('manage.layout')

@section('title', 'Gestion - Personnage')
@section('header', $character->display_name)

@section('content')
    @php
        $children = $character->childrenFromFather->merge($character->childrenFromMother)->unique('id');
        $accent = $character->preferred_color ?: '#8F6A3A';
        $formatDate = function ($date) {
            return $date ? $date->format('d/m/Y') : '-';
        };
        $youtubeId = null;
        if ($character->voice_youtube_url) {
            $youtubeUrl = trim((string) $character->voice_youtube_url);
            if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_-]{11})~', $youtubeUrl, $matches)) {
                $youtubeId = $matches[1];
            }
        }
    @endphp

    <style>
        .character-show {
            --accent: {{ $accent }};
        }
        .show-hero {
            display: grid;
            grid-template-columns: minmax(220px, 280px) 1fr;
            gap: 16px;
            padding: 14px;
            border-radius: 18px;
            border: 1px solid rgba(101,74,42,.24);
            background: linear-gradient(135deg, rgba(255,255,255,.75), rgba(255,255,255,.42));
            box-shadow: 0 14px 26px rgba(55,38,21,.13);
        }
        .show-portrait {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(101,74,42,.24);
            background: rgba(255,255,255,.42);
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .show-portrait img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .show-headline {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin: 0;
        }
        .show-headline h2 {
            margin: 0;
            font-size: 2rem;
            line-height: 1.1;
        }
        .role-chip,
        .status-chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 12px;
            font-size: .86rem;
            letter-spacing: .03em;
            border: 1px solid rgba(101,74,42,.32);
            background: rgba(255,255,255,.55);
        }
        .role-chip {
            background: color-mix(in srgb, var(--accent) 18%, white);
            color: #3d2a12;
            border-color: color-mix(in srgb, var(--accent) 48%, #6c4a2a);
        }
        .show-subtitle {
            margin: 6px 0 0;
            color: #5f421f;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
        }
        .meta-item {
            border-radius: 10px;
            border: 1px solid rgba(101,74,42,.2);
            padding: 8px 10px;
            background: rgba(255,255,255,.5);
        }
        .meta-item .k {
            display: block;
            font-size: .75rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #7b5e39;
        }
        .meta-item .v {
            display: block;
            margin-top: 2px;
            font-weight: 600;
            color: #2d1f12;
        }
        .section-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 14px;
        }
        .section-card {
            border-radius: 14px;
            border: 1px solid rgba(101,74,42,.24);
            background: rgba(255,255,255,.48);
            box-shadow: 0 10px 18px rgba(55,38,21,.08);
            padding: 12px;
        }
        .section-card h3 {
            margin: 0 0 8px;
            font-size: 1.05rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #6d4e2b;
        }
        .dl-row {
            margin: 0 0 7px;
        }
        .dl-row strong {
            color: #5f421f;
        }
        .full-width {
            grid-column: 1 / -1;
        }
        .gallery-slide {
            display: none;
        }
        .gallery-slide.is-active {
            display: block;
        }
        .gallery-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 1px solid rgba(80,59,32,.45);
            background: rgba(255,255,255,.45);
            cursor: pointer;
        }
        .gallery-dot.is-active {
            background: var(--accent);
            border-color: color-mix(in srgb, var(--accent) 62%, #6f4e2d);
        }
        .voice-wrap {
            border-radius: 12px;
            border: 1px dashed rgba(101,74,42,.34);
            background: rgba(255,255,255,.45);
            padding: 10px;
            margin-top: 8px;
        }
        .table-wrap {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid rgba(101,74,42,.2);
            background: rgba(255,255,255,.5);
        }
        .table-wrap table {
            margin: 0;
            width: 100%;
            color: #2f2011;
        }
        .table-wrap thead th {
            color: #6b451d;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .table-wrap tbody td {
            color: #2f2011;
            font-weight: 500;
        }
        .table-wrap tbody tr:nth-child(odd) {
            background: rgba(255,255,255,.52);
        }
        .table-wrap tbody tr:nth-child(even) {
            background: rgba(255,255,255,.35);
        }
        @media (max-width: 960px) {
            .show-hero,
            .section-grid {
                grid-template-columns: 1fr;
            }
            .meta-grid {
                grid-template-columns: 1fr;
            }
            .show-portrait {
                min-height: 220px;
            }
        }
    </style>

    <section class="panel character-show">
        <div class="show-hero">
            <div class="show-portrait">
                @if($character->image_path)
                    <img src="{{ route('media.show', ['path' => $character->image_path]) }}" alt="Portrait de {{ $character->display_name }}">
                @else
                    <p class="muted">Aucune photo</p>
                @endif
            </div>
            <div>
                <div class="show-headline">
                    <h2>{{ $character->display_name }}</h2>
                    <span class="role-chip">{{ $character->role ?: 'Rôle non défini' }}</span>
                    <span class="status-chip">{{ ucfirst($character->status ?: 'inconnu') }}</span>
                </div>
                <p class="show-subtitle">{{ $character->aliases ? 'Alias: ' . $character->aliases : 'Aucun alias' }}</p>

                <div class="meta-grid">
                    <div class="meta-item"><span class="k">Genre</span><span class="v">{{ $character->gender ?: '-' }}</span></div>
                    <div class="meta-item"><span class="k">Âge</span><span class="v">{{ $character->calculated_age !== null ? $character->calculated_age . ' ans' : '-' }}</span></div>
                    <div class="meta-item"><span class="k">Naissance</span><span class="v">{{ $formatDate($character->birth_date) }}</span></div>
                    <div class="meta-item"><span class="k">Décès</span><span class="v">{{ $formatDate($character->death_date) }}</span></div>
                    <div class="meta-item"><span class="k">Monde</span><span class="v">{{ optional($character->world)->name ?: '-' }}</span></div>
                    <div class="meta-item"><span class="k">Famille</span><span class="v">{{ $character->family_name ?: ($character->last_name ?: '-') }}</span></div>
                </div>
            </div>
        </div>

        <div class="section-grid">
            <article class="section-card">
                <h3>Famille et lieux</h3>
                <p class="dl-row"><strong>Père:</strong> {{ optional($character->father)->display_name ?: 'Inconnu' }}</p>
                <p class="dl-row"><strong>Mère:</strong> {{ optional($character->mother)->display_name ?: 'Inconnue' }}</p>
                <p class="dl-row"><strong>Conjoint:</strong> {{ optional($character->spouse)->display_name ?: '-' }}</p>
                @if($children->isNotEmpty())
                    <p class="dl-row"><strong>Enfants:</strong> {{ $children->pluck('display_name')->join(', ') }}</p>
                @endif
                <p class="dl-row"><strong>Lieu de naissance:</strong> {{ optional($character->birthPlace)->name ?: '-' }}</p>
                <p class="dl-row"><strong>Résidence:</strong> {{ optional($character->residencePlace)->name ?: '-' }}</p>
            </article>

            <article class="section-card">
                <h3>Pouvoir et objectifs</h3>
                @if($character->has_power || $character->power_level || trim((string) $character->power_description) !== '')
                    @if($character->power_level)
                        <p class="dl-row"><strong>Niveau:</strong> {{ $character->power_level }}/10</p>
                    @endif
                    @if(trim((string) $character->power_description) !== '')
                        <p class="dl-row"><strong>Pouvoir:</strong> {{ $character->power_description }}</p>
                    @endif
                @endif
                <p class="dl-row"><strong>Objectif court terme:</strong> {{ $character->short_term_goal ?: '-' }}</p>
                <p class="dl-row"><strong>Objectif long terme:</strong> {{ $character->long_term_goal ?: '-' }}</p>
                @if(trim((string) $character->secrets) !== '')
                    <p class="dl-row"><strong>Secret privé:</strong>
                        @if($character->secrets_is_private)
                            (masqué)
                        @else
                            {{ $character->secrets }}
                        @endif
                    </p>
                @endif
            </article>

            <article class="section-card">
                <h3>Psychologie</h3>
                <p class="dl-row"><strong>Qualités:</strong> {{ $character->qualities ?: '-' }}</p>
                <p class="dl-row"><strong>Défauts:</strong> {{ $character->flaws ?: '-' }}</p>

                <div class="voice-wrap">
                    <p class="dl-row"><strong>Voix:</strong></p>
                    @if($character->voice_audio_path)
                        <audio controls preload="none" style="width:100%;">
                            <source src="{{ route('media.show', ['path' => $character->voice_audio_path]) }}">
                        </audio>
                    @elseif($character->voice_youtube_url)
                        @if($youtubeId)
                            <iframe
                                width="420"
                                height="236"
                                src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                title="Voix de {{ $character->display_name }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen
                                style="max-width:100%; border-radius:10px; border:1px solid rgba(101,74,42,.28);"
                            ></iframe>
                        @else
                            <a href="{{ $character->voice_youtube_url }}" target="_blank" rel="noopener noreferrer">Écouter sur YouTube</a>
                        @endif
                    @else
                        <p class="muted">Aucune voix ajoutée.</p>
                    @endif
                </div>
            </article>

            <article class="section-card">
                <h3>Apparence</h3>
                <p class="dl-row"><strong>Taille:</strong> {{ $character->height ?: '-' }}</p>
                <p class="dl-row"><strong>Cheveux:</strong> {{ $character->hair_color ?: $character->hair_eyes ?: '-' }}</p>
                <p class="dl-row"><strong>Yeux:</strong> {{ $character->eye_color ?: $character->hair_eyes ?: '-' }}</p>
                <p class="dl-row"><strong>Marques:</strong> {{ $character->marks ?: '-' }}</p>
                <p class="dl-row"><strong>Style vestimentaire:</strong> {{ $character->clothing_style ?: '-' }}</p>
            </article>

            <article class="section-card full-width">
                <h3>Équipements et artefacts</h3>
                @if($character->items->isEmpty())
                    <p class="muted">Aucun équipement.</p>
                @else
                    <div class="table-wrap">
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
                    </div>
                @endif
            </article>

            <article class="section-card full-width">
                <h3>Métiers</h3>
                @if($character->jobs->isEmpty())
                    <p class="muted">Aucun métier.</p>
                @else
                    <div class="table-wrap">
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
                    </div>
                @endif
            </article>

            <article class="section-card full-width">
                <h3>Galerie</h3>
                @if($character->galleryImages->isEmpty())
                    <p class="muted">Aucune image de galerie.</p>
                @else
                    <div id="character-gallery-carousel" style="position:relative; max-width:860px; margin-top:10px;">
                        @foreach($character->galleryImages as $img)
                            <div class="gallery-slide{{ $loop->first ? ' is-active' : '' }}">
                                <div style="height:460px; display:flex; align-items:center; justify-content:center; border-radius:12px; border:1px solid rgba(101,74,42,.28); background:linear-gradient(180deg, rgba(34,27,20,.14), rgba(18,14,10,.22)); overflow:hidden;">
                                    <img
                                        src="{{ route('media.show', ['path' => $img->image_path]) }}"
                                        alt="Galerie {{ $character->display_name }}"
                                        style="max-width:100%; max-height:100%; width:auto; height:auto; object-fit:contain; image-orientation:from-image;"
                                    >
                                </div>
                                <div class="muted" style="margin-top:8px;">{{ $img->caption ?: 'Sans légende' }}</div>
                            </div>
                        @endforeach
                        @if($character->galleryImages->count() > 1)
                            <div id="gallery-dots" style="margin-top:10px; display:flex; gap:8px; justify-content:center;">
                                @foreach($character->galleryImages as $img)
                                    <button type="button" class="gallery-dot{{ $loop->first ? ' is-active' : '' }}" data-slide-index="{{ $loop->index }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </article>

            <article class="section-card full-width">
                <h3>Résumé</h3>
                <p>{{ $character->summary ?: 'Aucun résumé.' }}</p>
            </article>
        </div>

        <div class="stack" style="margin-top:14px;">
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

