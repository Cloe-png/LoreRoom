<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LoreRoom - Start</title>
        <style>
            :root {
                --bg-abyss: #121018;
                --bg-stone: #1f1b22;
                --ink: #eee6d6;
                --ember: #b9764a;
                --aether: #7aa7b0;
                --gold: #c7a46a;
                --mist: #b7ac9a;
                --ivory: #f6f0e6;
                --brass: #8f6a3f;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                color: var(--ink);
                background:
                    radial-gradient(900px 600px at 50% 40%, rgba(246, 240, 230, 0.18), transparent 65%),
                    radial-gradient(1200px 800px at 50% 12%, rgba(246, 240, 230, 0.08) 0%, transparent 60%),
                    linear-gradient(180deg, #1a1520 0%, #0e0d12 100%);
                font-family: "Garamond", "Georgia", serif;
                position: relative;
                overflow: hidden;
            }

            .museum-bg {
                position: absolute;
                inset: 0;
                z-index: 0;
                background-image: url('{{ asset('museum_background.jpg') }}');
                background-size: cover;
                background-position: center;
                filter: blur(2px) saturate(0.9);
                opacity: 0.2;
                transform: scale(1.03);
            }

            body::before {
                content: "";
                position: absolute;
                inset: -20% -10%;
                background:
                    radial-gradient(320px 220px at 15% 20%, rgba(199, 164, 106, 0.12), transparent 70%),
                    radial-gradient(280px 200px at 85% 25%, rgba(199, 164, 106, 0.12), transparent 70%),
                    radial-gradient(500px 300px at 50% 85%, rgba(14, 13, 18, 0.85), transparent 70%);
                filter: blur(10px);
                z-index: 0;
            }

            body::after {
                content: "";
                position: absolute;
                inset: 0;
                background:
                    linear-gradient(transparent 0%, rgba(12, 11, 18, 0.55) 55%, rgba(12, 11, 18, 0.9) 100%),
                    radial-gradient(1200px 500px at 50% 0%, rgba(246, 240, 230, 0.06), transparent 70%);
                z-index: 1;
            }

            .texture {
                position: absolute;
                inset: 0;
                z-index: 1;
                pointer-events: none;
                opacity: 0.55;
                background-image:
                    radial-gradient(2px 2px at 10% 20%, rgba(255, 255, 255, 0.08), transparent 60%),
                    radial-gradient(1.5px 1.5px at 30% 80%, rgba(255, 255, 255, 0.06), transparent 60%),
                    radial-gradient(1.2px 1.2px at 70% 40%, rgba(255, 255, 255, 0.05), transparent 60%),
                    repeating-linear-gradient(0deg, rgba(255, 255, 255, 0.03) 0px, rgba(255, 255, 255, 0.03) 1px, transparent 1px, transparent 4px),
                    repeating-linear-gradient(90deg, rgba(0, 0, 0, 0.03) 0px, rgba(0, 0, 0, 0.03) 1px, transparent 1px, transparent 5px),
                    radial-gradient(800px 600px at 50% 55%, rgba(199, 164, 106, 0.08), transparent 70%);
                mix-blend-mode: screen;
            }

            .paper {
                position: absolute;
                inset: 0;
                z-index: 1;
                pointer-events: none;
                opacity: 0.72;
                background:
                    radial-gradient(700px 500px at 50% 45%, rgba(234, 216, 186, 0.16), transparent 70%),
                    radial-gradient(1200px 800px at 50% 60%, rgba(140, 114, 78, 0.12), transparent 75%),
                    radial-gradient(900px 600px at 20% 30%, rgba(120, 96, 68, 0.08), transparent 75%),
                    radial-gradient(900px 600px at 80% 35%, rgba(120, 96, 68, 0.08), transparent 75%),
                    repeating-linear-gradient(10deg, rgba(90, 70, 50, 0.06) 0px, rgba(90, 70, 50, 0.06) 1px, transparent 1px, transparent 6px),
                    repeating-linear-gradient(-12deg, rgba(0, 0, 0, 0.04) 0px, rgba(0, 0, 0, 0.04) 1px, transparent 1px, transparent 8px);
                mix-blend-mode: multiply;
            }

            .divine-light {
                position: absolute;
                inset: -10% 0 0;
                z-index: 1;
                pointer-events: none;
                background:
                    radial-gradient(600px 280px at 50% 8%, rgba(255, 245, 220, 0.9), rgba(255, 214, 170, 0.35), transparent 70%),
                    radial-gradient(900px 360px at 50% 0%, rgba(255, 255, 255, 0.32), transparent 75%);
                filter: blur(6px);
                opacity: 0.7;
                animation: divinePulse 12s ease-in-out infinite;
            }

            .vignette {
                position: absolute;
                inset: 0;
                z-index: 1;
                pointer-events: none;
                background: radial-gradient(1200px 700px at 50% 45%, transparent 55%, rgba(6, 5, 8, 0.55) 100%);
                mix-blend-mode: multiply;
            }

            .carousel-bg {
                position: absolute;
                inset: 0;
                z-index: 1;
                pointer-events: none;
                display: grid;
                place-items: center;
            }

            .carousel-strip {
                width: min(1100px, 92vw);
                height: 180px;
                overflow: hidden;
                mask-image: linear-gradient(90deg, transparent 0%, #000 12%, #000 88%, transparent 100%);
                opacity: 0.65;
            }

            .carousel-track {
                display: flex;
                gap: 22px;
                width: max-content;
                animation: stripScroll 28s linear infinite;
            }

            .carousel-item {
                width: 140px;
                height: 180px;
                border-radius: 10px;
                border: 1px solid rgba(199, 164, 106, 0.3);
                background: rgba(18, 16, 24, 0.55);
                overflow: hidden;
                filter: blur(2px) saturate(0.8);
                box-shadow: 0 10px 24px rgba(0, 0, 0, 0.45);
            }

            .carousel-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            @keyframes stripScroll {
                0% { transform: translateX(0%); }
                100% { transform: translateX(-50%); }
            }

            .sparkles {
                position: absolute;
                inset: 0;
                z-index: 1;
                pointer-events: none;
                background-image:
                    radial-gradient(2px 2px at 20% 30%, rgba(246, 240, 230, 0.7), transparent 60%),
                    radial-gradient(1.5px 1.5px at 70% 40%, rgba(246, 240, 230, 0.6), transparent 60%),
                    radial-gradient(1.8px 1.8px at 40% 70%, rgba(246, 240, 230, 0.5), transparent 60%),
                    radial-gradient(1.2px 1.2px at 85% 65%, rgba(246, 240, 230, 0.5), transparent 60%);
                animation: sparkleFloat 16s linear infinite;
                opacity: 0.6;
            }

            .mist {
                position: absolute;
                inset: -10% 0 0;
                z-index: 1;
                pointer-events: none;
                background:
                    radial-gradient(700px 200px at 20% 80%, rgba(255, 255, 255, 0.06), transparent 70%),
                    radial-gradient(700px 220px at 80% 75%, rgba(255, 255, 255, 0.05), transparent 70%);
                filter: blur(12px);
                animation: mistDrift 22s ease-in-out infinite;
                opacity: 0.5;
            }

            @keyframes sparkleFloat {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
                100% { transform: translateY(0px); }
            }

            @keyframes mistDrift {
                0% { transform: translateX(0px); }
                50% { transform: translateX(18px); }
                100% { transform: translateX(0px); }
            }

            @keyframes divinePulse {
                0% { opacity: 0.6; }
                50% { opacity: 0.85; }
                100% { opacity: 0.6; }
            }

            .wrap {
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 32px 40px;
                position: relative;
                z-index: 2;
            }

            .panel {
                width: min(560px, 92vw);
                background:
                    linear-gradient(160deg, rgba(34, 30, 38, 0.55), rgba(16, 14, 20, 0.6)),
                    radial-gradient(220px 120px at 85% 15%, rgba(199, 164, 106, 0.14), transparent 60%);
                border: 1px solid rgba(199, 164, 106, 0.4);
                box-shadow: 0 30px 90px rgba(0, 0, 0, 0.65);
                border-radius: 10px;
                padding: 44px 48px 40px;
                position: relative;
                overflow: hidden;
                backdrop-filter: blur(16px);
                text-align: center;
            }

            .panel::before {
                content: "";
                position: absolute;
                inset: -45% 10% auto 10%;
                height: 240px;
                background: radial-gradient(closest-side, rgba(199, 164, 106, 0.22), transparent 70%);
                filter: blur(8px);
            }

            .logo {
                letter-spacing: 0.35em;
                text-transform: uppercase;
                font-size: 0.8rem;
                color: var(--mist);
            }

            .title-ornament {
                position: relative;
                margin: 8px auto 10px;
                width: min(260px, 70%);
                height: 32px;
            }

            .title-ornament::before,
            .title-ornament::after {
                content: "";
                position: absolute;
                top: 50%;
                width: 38%;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(199, 164, 106, 0.8), transparent);
            }

            .title-ornament::before { left: 0; }
            .title-ornament::after { right: 0; }

            .title-ornament span {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                color: rgba(199, 164, 106, 0.85);
                font-size: 1.2rem;
                letter-spacing: 0.4em;
            }

            h1 {
                font-family: "Cinzel", "Trajan Pro", "Times New Roman", serif;
                font-size: clamp(2.2rem, 6vw, 3.6rem);
                margin: 12px 0 6px;
                text-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            }

            .subtitle {
                color: var(--mist);
                font-size: 1.1rem;
                margin-bottom: 24px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
            }

            .menu {
                display: grid;
                gap: 10px;
                margin: 18px auto 10px;
                width: min(320px, 90%);
            }

            .menu a {
                display: block;
                padding: 14px 18px;
                border-radius: 10px;
                border: 1px solid rgba(199, 164, 106, 0.6);
                color: var(--ivory);
                text-decoration: none;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                font-size: 0.92rem;
                font-family: "Garamond", "Georgia", serif;
                background:
                    linear-gradient(140deg, rgba(199, 164, 106, 0.28), rgba(143, 106, 63, 0.22)),
                    radial-gradient(120px 40px at 50% 10%, rgba(246, 240, 230, 0.12), transparent 70%),
                    repeating-linear-gradient(90deg, rgba(0, 0, 0, 0.04) 0px, rgba(0, 0, 0, 0.04) 1px, transparent 1px, transparent 6px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.45);
                transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
                position: relative;
                overflow: hidden;
            }

            .menu a:hover {
                transform: translateY(-1px);
                box-shadow: 0 0 24px rgba(199, 164, 106, 0.35);
                border-color: rgba(199, 164, 106, 0.7);
            }

            .menu a::before {
                content: "";
                position: absolute;
                inset: 8px;
                border-radius: 8px;
                border: 1px solid rgba(246, 240, 230, 0.2);
                pointer-events: none;
            }

            .menu a::after {
                content: "";
                position: absolute;
                inset: -6px;
                border-radius: 14px;
                border: 1px solid rgba(199, 164, 106, 0.28);
                opacity: 0;
                transition: opacity 0.2s ease;
                pointer-events: none;
            }

            .menu a:hover::after {
                opacity: 1;
            }

            .menu a.is-selected {
                border-color: rgba(199, 164, 106, 0.85);
                box-shadow: 0 0 24px rgba(199, 164, 106, 0.4);
                transform: translateY(-1px);
            }

            .menu a span {
                position: relative;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                z-index: 1;
                text-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
            }

            .menu a span::before {
                content: "*";
                color: rgba(199, 164, 106, 0.9);
                font-size: 0.85rem;
                letter-spacing: 0.2em;
            }

            .menu a[aria-disabled="true"] {
                opacity: 0.5;
                filter: saturate(0.7);
                cursor: default;
                pointer-events: none;
            }

            .sound-toggle {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                margin-top: 14px;
                padding: 8px 14px;
                border-radius: 999px;
                border: 1px solid rgba(199, 164, 106, 0.45);
                background: rgba(18, 16, 24, 0.5);
                color: rgba(231, 221, 200, 0.85);
                font-size: 0.85rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                cursor: pointer;
            }

            .sound-toggle .dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: rgba(199, 164, 106, 0.9);
                box-shadow: 0 0 12px rgba(199, 164, 106, 0.7);
            }

            .sound-toggle.is-muted .dot {
                background: rgba(176, 169, 154, 0.6);
                box-shadow: none;
            }

            .version {
                margin-top: 14px;
                font-size: 0.75rem;
                letter-spacing: 0.2em;
                color: rgba(199, 164, 106, 0.5);
                text-transform: uppercase;
            }

            .top-controls {
                position: absolute;
                top: 18px;
                right: 18px;
                display: flex;
                gap: 10px;
                z-index: 3;
            }

            .toggle-btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 10px;
                border-radius: 999px;
                border: 1px solid rgba(199, 164, 106, 0.45);
                background: rgba(18, 16, 24, 0.55);
                color: rgba(231, 221, 200, 0.85);
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                cursor: pointer;
            }

            .toggle-btn .dot {
                width: 9px;
                height: 9px;
                border-radius: 50%;
                background: rgba(199, 164, 106, 0.9);
                box-shadow: 0 0 10px rgba(199, 164, 106, 0.7);
            }

            .toggle-btn.is-muted .dot {
                background: rgba(176, 169, 154, 0.6);
                box-shadow: none;
            }

            @media (max-width: 900px) {
                .wrap {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <div class="museum-bg" aria-hidden="true"></div>
        <div class="paper" aria-hidden="true"></div>
        <div class="texture" aria-hidden="true"></div>
        <div class="sparkles" aria-hidden="true"></div>
        <div class="divine-light" aria-hidden="true"></div>
        <div class="mist" aria-hidden="true"></div>
        <div class="vignette" aria-hidden="true"></div>
        <div class="carousel-bg" aria-hidden="true">
            @php
                $images = [
                    'Baby Isan hurt.jpg',
                    'Father and son.jpg',
                    'Little Martin drink.png',
                    'Maxime K.O.jpg',
                ];
            @endphp
            <div class="carousel-strip">
                <div class="carousel-track">
                    @foreach (array_merge($images, $images) as $image)
                        <div class="carousel-item">
                            <img src="{{ asset(rawurlencode($image)) }}" alt="">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="top-controls" aria-label="Audio">
            <button class="toggle-btn" id="soundToggle" type="button">
                <span class="dot" aria-hidden="true"></span>
                Son
            </button>
            <button class="toggle-btn" id="musicToggle" type="button">
                <span class="dot" aria-hidden="true"></span>
                Musique
            </button>
        </div>
        <div class="wrap">
            <main class="panel">
                <div class="logo">Grimoire d'Univers</div>
                <h1>LoreRoom</h1>
                <div class="title-ornament"><span>*</span></div>
                <nav class="menu" aria-label="Menu principal">
                    <a id="startBtn" href="{{ url('/portals') }}"><span>Start</span></a>
                </nav>
                <div class="version">v0.1</div>
            </main>
        </div>
        <script>
            const soundToggle = document.getElementById('soundToggle');
            const musicToggle = document.getElementById('musicToggle');

            soundToggle.addEventListener('click', () => {
                soundToggle.classList.toggle('is-muted');
            });

            musicToggle.addEventListener('click', () => {
                musicToggle.classList.toggle('is-muted');
            });

            const quitBtn = document.getElementById('quitBtn');
            quitBtn.addEventListener('click', (event) => {
                event.preventDefault();
                window.close();
            });

            const menuLinks = Array.from(document.querySelectorAll('.menu a'));
            let selectedIndex = 0;

            const updateSelection = () => {
                menuLinks.forEach((link, index) => {
                    link.classList.toggle('is-selected', index === selectedIndex);
                });
            };

            updateSelection();

            document.addEventListener('keydown', (event) => {
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    selectedIndex = (selectedIndex + 1) % menuLinks.length;
                    updateSelection();
                }

                if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    selectedIndex = (selectedIndex - 1 + menuLinks.length) % menuLinks.length;
                    updateSelection();
                }

                if (event.key === 'Enter') {
                    event.preventDefault();
                    menuLinks[selectedIndex].click();
                }
            });
        </script>
    </body>
</html>
