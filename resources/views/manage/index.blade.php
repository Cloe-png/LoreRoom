@extends('manage.layout')

@section('title', 'Gestion - Accueil')
@section('header', 'Carnet de bord')

@section('content')
    <style>
        .journal {
            display: grid;
            gap: 14px;
            position: relative;
            z-index: 3;
        }

        .journal-cover {
            position: relative;
            border: 1px solid rgba(88, 61, 34, .35);
            border-radius: 12px;
            padding: 18px 16px;
            background:
                linear-gradient(180deg, rgba(214, 184, 140, .58), rgba(178, 142, 99, .48)),
                repeating-linear-gradient(90deg, rgba(77, 52, 27, .08) 0 2px, transparent 2px 11px),
                radial-gradient(780px 230px at 8% -12%, rgba(255, 239, 203, .3), transparent 66%);
            box-shadow: 0 12px 26px rgba(65, 42, 22, .2);
            overflow: hidden;
        }

        .journal-cover::after {
            content: "";
            position: absolute;
            right: -22px;
            top: -22px;
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(118, 84, 46, .24) 0%, rgba(118, 84, 46, 0) 72%);
            pointer-events: none;
        }

        .journal-title {
            margin: 0;
            color: #3a2614;
            font-family: "Cinzel", "Times New Roman", serif;
            letter-spacing: .04em;
            font-size: clamp(1.25rem, 2vw, 1.7rem);
        }

        .journal-subtitle {
            margin: 7px 0 0;
            color: #5d4326;
            max-width: 760px;
            font-size: .95rem;
            line-height: 1.4;
        }

        .journal-meta {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px dashed rgba(104, 73, 40, .45);
            border-radius: 999px;
            background: rgba(255, 246, 226, .52);
            color: #51371d;
            padding: 5px 11px;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .journal-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(12, 1fr);
        }

        .sheet {
            grid-column: span 12;
            border: 1px solid rgba(114, 84, 49, .3);
            border-radius: 10px;
            background:
                linear-gradient(180deg, rgba(248, 237, 215, .98), rgba(235, 218, 186, .97)),
                repeating-linear-gradient(0deg, transparent 0 29px, rgba(73, 120, 177, .18) 29px 30px);
            color: #322514;
            box-shadow: 0 8px 20px rgba(76, 53, 30, .14);
            padding: 14px;
            position: relative;
        }

        .sheet::before {
            content: "";
            position: absolute;
            left: 24px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(188, 72, 72, .35);
            opacity: .9;
        }

        .sheet-content {
            padding-left: 24px;
        }

        .sheet-title {
            margin: 0 0 8px;
            color: #4a3117;
            font-family: "Segoe Print", "Comic Sans MS", cursive;
            font-size: 1.12rem;
        }

        .stats {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        }

        .sticky {
            border: 1px solid rgba(109, 78, 45, .25);
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 8px 15px rgba(50, 34, 17, .11);
            transform: rotate(var(--tilt));
            min-height: 92px;
        }

        .sticky:nth-child(1) { --tilt: -1deg; background: linear-gradient(180deg, #f8dfa3, #ebc271); }
        .sticky:nth-child(2) { --tilt: .8deg; background: linear-gradient(180deg, #c6e2d5, #9dc9b7); }
        .sticky:nth-child(3) { --tilt: -0.7deg; background: linear-gradient(180deg, #c6d7ef, #a5bee2); }
        .sticky:nth-child(4) { --tilt: .9deg; background: linear-gradient(180deg, #e8c8c8, #dca6a6); }

        .sticky-label {
            color: #5f4628;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .sticky-value {
            margin-top: 6px;
            color: #2e2113;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1.95rem;
            line-height: 1;
        }

        .columns {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .entry-list {
            margin: 6px 0 0;
            padding-left: 18px;
        }

        .entry-list li + li {
            margin-top: 5px;
        }

        .ledger {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .ledger th,
        .ledger td {
            border-bottom: 1px dashed rgba(102, 73, 41, .34);
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
            color: #3f2d1a;
        }

        .ledger th {
            color: #674620;
            text-transform: uppercase;
            letter-spacing: .05em;
            font-size: .78rem;
            font-family: "Cinzel", "Times New Roman", serif;
        }

        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            padding-left: 24px;
        }

        .empty-note {
            margin: 6px 0 0;
            color: #6d5234;
            font-size: .92rem;
            font-style: italic;
        }

        .birthday-blast {
            position: relative;
            border: 1px solid rgba(113, 74, 34, .35);
            border-radius: 18px;
            padding: 18px;
            background:
                radial-gradient(560px 180px at 10% -10%, rgba(255, 244, 196, .9), transparent 60%),
                linear-gradient(180deg, rgba(255, 236, 194, .96), rgba(245, 208, 154, .88));
            box-shadow: 0 14px 24px rgba(99, 62, 24, .2);
            overflow: hidden;
        }

        .birthday-blast::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: repeating-linear-gradient(135deg, rgba(255,255,255,.1) 0 14px, rgba(0,0,0,0) 14px 28px);
            opacity: .45;
            z-index: 0;
        }

        .birthday-head {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .birthday-head-left {
            min-width: 0;
        }

        .birthday-head-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .birthday-title {
            margin: 0;
            color: #512f12;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: clamp(1.2rem, 2.4vw, 2rem);
            text-shadow: 0 1px 0 rgba(255,255,255,.5);
        }

        .birthday-sub {
            margin: 4px 0 0;
            color: #6b4b2a;
            font-size: .9rem;
        }

        .birthday-cake {
            width: 58px;
            height: 46px;
            border-radius: 10px 10px 8px 8px;
            background: linear-gradient(180deg, #f8d2dc 0 32%, #ffe8a7 32% 64%, #f0bf88 64% 100%);
            border: 1px solid rgba(101, 63, 27, .35);
            box-shadow: 0 8px 14px rgba(82, 50, 20, .22);
            position: relative;
            flex: 0 0 auto;
        }

        .birthday-cake::before {
            content: "";
            position: absolute;
            width: 8px;
            height: 14px;
            left: 25px;
            top: -13px;
            background: linear-gradient(180deg, #ffe4a2, #f3b54a);
            border-radius: 3px;
            border: 1px solid rgba(104, 66, 28, .3);
        }

        .birthday-cake::after {
            content: "";
            position: absolute;
            width: 10px;
            height: 10px;
            left: 24px;
            top: -22px;
            background: radial-gradient(circle, #fff7cd 0, #ffc460 60%, #ff9b27 100%);
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(255, 174, 50, .7);
        }

        .birthday-music-btn {
            border: 1px solid rgba(88, 57, 23, .35);
            border-radius: 999px;
            padding: 7px 12px;
            background: rgba(255, 255, 255, .75);
            color: #4a2f16;
            font-size: .8rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(75, 47, 22, .14);
        }

        .birthday-music-btn:hover {
            background: rgba(255, 255, 255, .9);
        }

        .birthday-list {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }

        .birthday-card {
            display: flex;
            gap: 10px;
            align-items: center;
            border: 1px solid rgba(111, 73, 35, .28);
            border-radius: 14px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.92), rgba(252,245,229,.88));
            padding: 12px;
            text-decoration: none;
            color: #3b2815;
            box-shadow: 0 7px 14px rgba(81, 51, 22, .12);
            transition: transform .14s ease, box-shadow .14s ease;
            position: relative;
            overflow: hidden;
        }

        .birthday-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 5px;
            background: linear-gradient(180deg, #c77d2b, #8f4f12);
            opacity: .7;
        }

        .birthday-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 18px rgba(81, 51, 22, .18);
        }

        .birthday-photo-wrap {
            position: relative;
            width: 72px;
            height: 72px;
            flex: 0 0 72px;
        }

        .birthday-photo {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center 18%;
            border: 3px solid rgba(90, 59, 27, .35);
            background: #f5ead8;
            box-shadow: 0 6px 14px rgba(56, 34, 12, .18);
        }

        .birthday-fallback {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 3px solid rgba(90, 59, 27, .35);
            display: grid;
            place-items: center;
            background: linear-gradient(180deg, #f7ebd4, #e9d0a2);
            color: #6b471f;
            font-weight: 800;
            box-shadow: 0 6px 14px rgba(56, 34, 12, .18);
        }

        .party-hat {
            position: absolute;
            width: 24px;
            height: 26px;
            left: 48px;
            top: -6px;
            clip-path: polygon(50% 0, 0 100%, 100% 100%);
            background: linear-gradient(180deg, #7b4cd5, #d35fa7);
            border: 1px solid rgba(49, 27, 98, .45);
            transform: rotate(16deg);
            box-shadow: 0 5px 10px rgba(0, 0, 0, .16);
            z-index: 3;
        }

        .party-hat::after {
            content: "";
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ffeaa5;
            top: -6px;
            left: 9px;
            border: 1px solid rgba(119, 84, 24, .35);
        }

        .birthday-name {
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1.08rem;
            color: #4d2f14;
        }

        .birthday-world {
            color: #6a4d30;
            font-size: .84rem;
        }

        .confetti-canvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        @media (max-width: 980px) {
            .columns {
                grid-template-columns: 1fr;
            }

            .birthday-head {
                align-items: flex-start;
            }

            .birthday-head-actions {
                width: 100%;
                justify-content: space-between;
            }

            .birthday-music-btn {
                font-size: .78rem;
            }
        }
    </style>

    <div class="journal">
        @if($todayBirthdays->isNotEmpty())
            <section class="birthday-blast">
                <canvas id="birthday-confetti" class="confetti-canvas" aria-hidden="true"></canvas>
                <div class="birthday-head">
                    <div class="birthday-head-left">
                        <h2 class="birthday-title">Joyeux anniversaire !</h2>
                        <p class="birthday-sub">Célébration du jour pour tes personnages.</p>
                    </div>
                    <div class="birthday-head-actions">
                        <button type="button" class="birthday-music-btn" id="birthday-music-toggle">Couper la musique</button>
                        <div class="birthday-cake" aria-hidden="true"></div>
                    </div>
                </div>
                <div class="birthday-list">
                    @foreach($todayBirthdays as $character)
                        @php
                            $initial = strtoupper(mb_substr((string) $character->display_name, 0, 1));
                        @endphp
                        <a class="birthday-card" href="{{ route('manage.characters.show', $character) }}">
                            <div class="birthday-photo-wrap">
                                @if($character->image_path)
                                    <img class="birthday-photo" src="{{ route('media.show', ['path' => $character->image_path]) }}" alt="Photo de {{ $character->display_name }}">
                                @else
                                    <div class="birthday-fallback">{{ $initial ?: '?' }}</div>
                                @endif
                                <span class="party-hat" aria-hidden="true"></span>
                            </div>
                            <div>
                                <div class="birthday-name">{{ $character->display_name }}</div>
                                <div class="birthday-world">{{ optional($character->world)->name ?: 'Monde inconnu' }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
            <iframe
                id="birthday-music-frame"
                width="0"
                height="0"
                style="position:absolute; left:-9999px; border:0;"
                allow="autoplay; encrypted-media"
                src="https://www.youtube.com/embed/FhNGLVJKsBg?autoplay=1&loop=1&playlist=FhNGLVJKsBg&controls=0&disablekb=1&modestbranding=1&rel=0"
                title="Musique anniversaire"
            ></iframe>
        @endif

        <section class="journal-cover">
            <h2 class="journal-title">Carnet des événements</h2>
            <div class="journal-meta">{{ ucfirst($today->locale('fr')->translatedFormat('l j F Y')) }}</div>
            <div class="quick-actions">
                <a class="btn" href="{{ route('manage.chronicles.create') }}">Ajouter un événement</a>
                <a class="btn secondary" href="{{ route('manage.characters.create') }}">Ajouter un personnage</a>
            </div>
        </section>

        <section class="sheet">
            <div class="sheet-content">
                <div class="stats">
                    <article class="sticky">
                        <div class="sticky-label">Mondes</div>
                        <div class="sticky-value">{{ $worldsCount }}</div>
                    </article>
                    <article class="sticky">
                        <div class="sticky-label">Personnages</div>
                        <div class="sticky-value">{{ $charactersCount }}</div>
                    </article>
                    <article class="sticky">
                        <div class="sticky-label">Chroniques</div>
                        <div class="sticky-value">{{ $chroniclesCount }}</div>
                    </article>
                    <article class="sticky">
                        <div class="sticky-label">Lieux</div>
                        <div class="sticky-value">{{ $placesCount }}</div>
                    </article>
                </div>
            </div>
        </section>

        <section class="sheet">
            <div class="sheet-content">
                <h3 class="sheet-title">Entrées du jour</h3>
                <div class="columns">
                    <div>
                        <strong>Anniversaires</strong>
                        @if($todayBirthdays->isEmpty())
                            <p class="empty-note">Aucun anniversaire aujourd'hui.</p>
                        @else
                            <ul class="entry-list">
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

                    <div>
                        <strong>Frise chrono (aujourd'hui)</strong>
                        @if($todayChronicles->isEmpty())
                            <p class="empty-note">Aucun événement prévu aujourd'hui.</p>
                        @else
                            <ul class="entry-list">
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
            </div>
        </section>

        <section class="sheet">
            <div class="sheet-content">
                <h3 class="sheet-title">À venir (14 jours)</h3>
                @if($upcomingChronicles->isEmpty())
                    <p class="empty-note">Aucun événement à venir sur les 14 prochains jours.</p>
                @else
                    <table class="ledger">
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
                                    <td>{{ optional($chronicle->event_date)->locale('fr')->translatedFormat('d/m/Y') }}</td>
                                    <td>{{ $chronicle->title }}</td>
                                    <td>{{ optional($chronicle->world)->name ?: '-' }}</td>
                                    <td>{{ $chronicle->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </section>
    </div>

    @if($todayBirthdays->isNotEmpty())
        <script>
            (function () {
                const canvas = document.getElementById('birthday-confetti');
                if (!canvas) return;
                const host = canvas.closest('.birthday-blast');
                if (!host) return;
                const ctx = canvas.getContext('2d');
                const pieces = [];
                const colors = ['#E63946', '#F1C453', '#2A9D8F', '#4F6DCC', '#F28482', '#8B5CF6'];

                function resize() {
                    canvas.width = host.clientWidth;
                    canvas.height = host.clientHeight;
                }

                function spawn(count) {
                    for (let i = 0; i < count; i++) {
                        pieces.push({
                            x: Math.random() * canvas.width,
                            y: Math.random() * canvas.height - canvas.height,
                            w: 4 + Math.random() * 7,
                            h: 7 + Math.random() * 9,
                            c: colors[Math.floor(Math.random() * colors.length)],
                            a: 0.35 + Math.random() * 0.45,
                            vx: -0.9 + Math.random() * 1.8,
                            vy: 1.4 + Math.random() * 2.6,
                            rot: Math.random() * Math.PI,
                            vr: -0.07 + Math.random() * 0.14,
                        });
                    }
                }

                function tick() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    for (let i = 0; i < pieces.length; i++) {
                        const p = pieces[i];
                        p.x += p.vx;
                        p.y += p.vy;
                        p.rot += p.vr;
                        if (p.y > canvas.height + 20) {
                            p.y = -20;
                            p.x = Math.random() * canvas.width;
                        }
                        ctx.save();
                        ctx.translate(p.x, p.y);
                        ctx.rotate(p.rot);
                        ctx.globalAlpha = p.a;
                        ctx.fillStyle = p.c;
                        ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                        ctx.restore();
                    }
                    requestAnimationFrame(tick);
                }

                resize();
                spawn(88);
                tick();
                window.addEventListener('resize', resize);
            })();
        </script>
        <script>
            (function () {
                const frame = document.getElementById('birthday-music-frame');
                const toggle = document.getElementById('birthday-music-toggle');
                if (!frame || !toggle) return;

                const musicUrl = 'https://www.youtube.com/embed/FhNGLVJKsBg?autoplay=1&loop=1&playlist=FhNGLVJKsBg&controls=0&disablekb=1&modestbranding=1&rel=0';
                let enabled = true;

                toggle.addEventListener('click', function () {
                    enabled = !enabled;
                    if (enabled) {
                        frame.src = musicUrl;
                        toggle.textContent = 'Couper la musique';
                    } else {
                        frame.src = 'about:blank';
                        toggle.textContent = 'Remettre la musique';
                    }
                });
            })();
        </script>
    @endif
@endsection
