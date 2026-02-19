<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LoreRoom - Gestion</title>
        <style>
            :root {
                --paper: #efe2c9;
                --paper-ink: #3b2d1c;
                --gold: #caa46a;
                --gold-dark: #7a5931;
                --stone-1: #111723;
                --stone-2: #1c2432;
                --emerald: #2f6158;
                --danger: #a93232;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                color: #e9dfce;
                font-family: "Garamond", "Georgia", serif;
                background:
                    radial-gradient(860px 420px at 50% 2%, rgba(243, 220, 178, 0.2), transparent 68%),
                    linear-gradient(180deg, var(--stone-2) 0%, var(--stone-1) 100%);
                position: relative;
                overflow-x: hidden;
            }

            .bg {
                position: fixed;
                inset: 0;
                background-image: url('{{ asset('museum_background.jpg') }}');
                background-size: cover;
                background-position: center;
                filter: blur(5px) saturate(0.82);
                opacity: 0.18;
                transform: scale(1.04);
                z-index: 0;
            }

            .noise {
                position: fixed;
                inset: 0;
                pointer-events: none;
                z-index: 1;
                background:
                    radial-gradient(850px 260px at 50% -4%, rgba(255, 238, 203, 0.35), transparent 74%),
                    repeating-linear-gradient(0deg, rgba(255, 255, 255, 0.02) 0px, rgba(255, 255, 255, 0.02) 1px, transparent 1px, transparent 5px);
            }

            .wrap {
                min-height: 100vh;
                position: relative;
                z-index: 2;
                padding: 34px 16px;
                display: grid;
                place-items: center;
            }

            .shell {
                width: min(1180px, 96vw);
                border-radius: 16px;
                border: 1px solid rgba(202, 164, 106, 0.45);
                background:
                    linear-gradient(145deg, rgba(29, 37, 52, 0.82), rgba(14, 19, 28, 0.86)),
                    radial-gradient(300px 140px at 12% 8%, rgba(202, 164, 106, 0.12), transparent 72%),
                    radial-gradient(300px 140px at 88% 8%, rgba(202, 164, 106, 0.12), transparent 72%);
                box-shadow: 0 32px 100px rgba(0, 0, 0, 0.62);
                backdrop-filter: blur(10px);
                padding: 20px;
                position: relative;
                overflow: hidden;
            }

            .shell::before {
                content: "";
                position: absolute;
                inset: 10px;
                border: 1px solid rgba(242, 219, 176, 0.22);
                border-radius: 10px;
                pointer-events: none;
            }

            .header {
                display: flex;
                justify-content: space-between;
                align-items: end;
                gap: 16px;
                margin-bottom: 18px;
            }

            h1 {
                margin: 0;
                font-family: "Cinzel", "Trajan Pro", "Times New Roman", serif;
                letter-spacing: 0.04em;
                font-size: clamp(1.8rem, 4.2vw, 3rem);
                text-shadow: 0 4px 14px rgba(0, 0, 0, 0.5);
            }

            .stamp {
                padding: 6px 10px;
                border-radius: 999px;
                border: 1px solid rgba(240, 214, 168, 0.45);
                color: #e7d3ac;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                font-size: 0.67rem;
                white-space: nowrap;
                background: rgba(13, 18, 29, 0.55);
            }

            .subtitle {
                margin: 0 0 20px;
                color: #cdbda2;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                font-size: 0.8rem;
            }

            .layout {
                display: grid;
                grid-template-columns: 280px 1fr;
                gap: 16px;
            }

            .codex {
                border-radius: 12px;
                border: 1px solid rgba(237, 214, 172, 0.22);
                background:
                    linear-gradient(180deg, rgba(22, 29, 41, 0.74), rgba(11, 15, 23, 0.84));
                padding: 16px 14px;
            }

            .codex h2 {
                margin: 0 0 10px;
                font-size: 1.06rem;
                letter-spacing: 0.04em;
                font-family: "Cinzel", "Trajan Pro", serif;
                color: #ecd8b0;
            }

            .codex p {
                margin: 0 0 12px;
                color: #baa98e;
                font-size: 0.95rem;
            }

            .codex ul {
                margin: 0;
                padding: 0;
                list-style: none;
                display: grid;
                gap: 8px;
            }

            .codex li {
                padding: 9px 10px;
                border-radius: 7px;
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(232, 203, 150, 0.14);
                color: #d7c29a;
                font-size: 0.9rem;
            }

            .board {
                display: grid;
                gap: 14px;
            }

            .paper {
                border-radius: 12px;
                background:
                    linear-gradient(180deg, rgba(238, 224, 197, 0.96), rgba(220, 199, 166, 0.95)),
                    repeating-linear-gradient(10deg, rgba(111, 84, 53, 0.07) 0px, rgba(111, 84, 53, 0.07) 1px, transparent 1px, transparent 6px);
                border: 1px solid rgba(255, 245, 224, 0.6);
                box-shadow: 0 14px 28px rgba(0, 0, 0, 0.35);
                color: var(--paper-ink);
                padding: 16px 16px 14px;
            }

            .paper h3 {
                margin: 0 0 8px;
                font-family: "Cinzel", "Trajan Pro", serif;
                letter-spacing: 0.03em;
                font-size: 1.1rem;
            }

            .paper p {
                margin: 0;
                line-height: 1.38;
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
                gap: 12px;
            }

            .module {
                border-radius: 10px;
                padding: 14px 12px;
                border: 1px solid rgba(104, 79, 48, 0.34);
                background:
                    linear-gradient(180deg, rgba(243, 233, 213, 0.95), rgba(226, 209, 179, 0.96));
                box-shadow: inset 0 0 0 1px rgba(255, 245, 223, 0.55);
            }

            .module .tag {
                display: inline-block;
                margin-bottom: 8px;
                padding: 3px 7px;
                border-radius: 999px;
                border: 1px solid rgba(121, 85, 45, 0.35);
                font-size: 0.68rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #5a4126;
                background: rgba(255, 249, 236, 0.6);
            }

            .module h4 {
                margin: 0 0 5px;
                font-family: "Cinzel", "Trajan Pro", serif;
                font-size: 1rem;
                letter-spacing: 0.02em;
            }

            .module p {
                margin: 0;
                font-size: 0.9rem;
                color: #5f4730;
            }

            .actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 2px;
            }

            .btn {
                display: inline-block;
                padding: 9px 12px;
                border-radius: 8px;
                border: 1px solid rgba(255, 241, 208, 0.42);
                background: linear-gradient(180deg, #3d776f, #28564f);
                color: #f4efe4;
                text-decoration: none;
                font-size: 0.9rem;
                letter-spacing: 0.03em;
            }

            .btn.secondary {
                background: linear-gradient(180deg, #4a3a26, #342716);
            }

            .warning {
                margin-top: 10px;
                padding: 10px 12px;
                border-radius: 8px;
                border: 1px solid rgba(169, 50, 50, 0.42);
                background: rgba(90, 24, 24, 0.18);
                color: #f0c9c9;
                font-size: 0.9rem;
            }

            @media (max-width: 900px) {
                .layout {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <div class="bg" aria-hidden="true"></div>
        <div class="noise" aria-hidden="true"></div>
        <div class="wrap">
            <main class="shell">
                <header class="header">
                    <h1>Portail de Gestion</h1>
                    <span class="stamp">Mode Conservateur</span>
                </header>
                <p class="subtitle">Atelier de conception du lore: structures, chroniques et coherence du monde.</p>

                <section class="layout">
                    <aside class="codex">
                        <h2>Codex</h2>
                        <p>Controle editorial du monde et de ses archives.</p>
                        <ul>
                            <li>Chronologie maitresse</li>
                            <li>Index des personnages</li>
                            <li>Lieux, cartes et factions</li>
                            <li>Objets, reliques et savoirs</li>
                            <li>Validation de coherence</li>
                        </ul>
                    </aside>

                    <div class="board">
                        <article class="paper">
                            <h3>Table de Rédaction</h3>
                            <p>Une interface de travail inspiree des codex fantasy: lecture claire, priorites visibles, et modules de creation prets a l emploi.</p>
                        </article>

                        <article class="paper">
                            <div class="grid">
                                <section class="module">
                                    <span class="tag">Monde</span>
                                    <h4>Panorama global</h4>
                                    <p>Cadre de reference, eras, peuples et grandes tensions.</p>
                                </section>
                                <section class="module">
                                    <span class="tag">Personnages</span>
                                    <h4>Fiches narratives</h4>
                                    <p>Motivations, liens, arcs et contradictions dramatiques.</p>
                                </section>
                                <section class="module">
                                    <span class="tag">Lieux</span>
                                    <h4>Atlas interne</h4>
                                    <p>Regions, cites, points d interet et reperes visuels.</p>
                                </section>
                                <section class="module">
                                    <span class="tag">Chroniques</span>
                                    <h4>Ligne du temps</h4>
                                    <p>Événements majeurs, périodes charnières et répercussions.</p>
                                </section>
                            </div>
                        </article>

                        <article class="paper">
                            <div class="actions">
                                <a class="btn" href="{{ route('login') }}">Retour à la connexion</a>
                                <a class="btn secondary" href="{{ url('/story') }}">Ouvrir le portail recit</a>
                            </div>
                            <div class="warning">Section atelier en version prototype. Certaines actions de gestion seront activees ensuite.</div>
                        </article>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>

