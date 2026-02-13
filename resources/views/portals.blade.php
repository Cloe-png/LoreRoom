<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LoreRoom - Portals</title>
        <style>
            :root {
                --ink: #efe6d4;
                --ink-soft: #cbbba0;
                --gold-1: #d5b47a;
                --gold-2: #8c6637;
                --night-1: #0d1118;
                --night-2: #171d28;
                --emerald-1: #40796f;
                --emerald-2: #1f4f48;
                --steel-1: #5f6f89;
                --steel-2: #3d485d;
                --danger: #a82a2a;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                color: var(--ink);
                font-family: "Garamond", "Georgia", serif;
                background:
                    radial-gradient(900px 540px at 50% -2%, rgba(255, 244, 214, 0.2), transparent 68%),
                    linear-gradient(180deg, #1b202b 0%, #0d1118 100%);
                overflow-x: hidden;
                position: relative;
            }

            .museum-bg {
                position: fixed;
                inset: 0;
                background-image: url('{{ asset('museum_background.jpg') }}');
                background-size: cover;
                background-position: center;
                opacity: 0.22;
                filter: blur(4px) saturate(0.9);
                transform: scale(1.03);
                z-index: 0;
            }

            .ambience {
                position: fixed;
                inset: 0;
                pointer-events: none;
                z-index: 1;
                background:
                    radial-gradient(720px 340px at 50% 6%, rgba(255, 238, 205, 0.34), transparent 72%),
                    radial-gradient(1000px 520px at 50% 110%, rgba(26, 19, 12, 0.64), transparent 70%),
                    repeating-linear-gradient(0deg, rgba(255, 255, 255, 0.02) 0px, rgba(255, 255, 255, 0.02) 1px, transparent 1px, transparent 5px);
            }

            .wrap {
                min-height: 100vh;
                position: relative;
                z-index: 2;
                display: grid;
                place-items: center;
                padding: 40px 18px;
            }

            .panel {
                width: min(1120px, 96vw);
                border-radius: 16px;
                border: 1px solid rgba(213, 180, 122, 0.46);
                background:
                    linear-gradient(160deg, rgba(29, 35, 48, 0.82), rgba(15, 19, 28, 0.86)),
                    radial-gradient(340px 180px at 12% 6%, rgba(213, 180, 122, 0.12), transparent 74%),
                    radial-gradient(340px 180px at 88% 6%, rgba(213, 180, 122, 0.12), transparent 74%);
                box-shadow: 0 34px 120px rgba(0, 0, 0, 0.64);
                padding: 28px 28px 30px;
                backdrop-filter: blur(12px);
                position: relative;
                overflow: hidden;
            }

            .panel::before {
                content: "";
                position: absolute;
                inset: 10px;
                border: 1px solid rgba(237, 210, 154, 0.24);
                border-radius: 10px;
                pointer-events: none;
            }

            .topline {
                display: flex;
                align-items: end;
                justify-content: space-between;
                gap: 16px;
                margin-bottom: 18px;
                position: relative;
                z-index: 2;
            }

            h1 {
                margin: 0;
                font-family: "Cinzel", "Trajan Pro", "Times New Roman", serif;
                font-size: clamp(2rem, 4.4vw, 3.25rem);
                letter-spacing: 0.03em;
                text-shadow: 0 4px 16px rgba(0, 0, 0, 0.5);
            }

            .meta {
                text-transform: uppercase;
                font-size: 0.72rem;
                letter-spacing: 0.16em;
                color: #d8c8ac;
                opacity: 0.82;
                text-align: right;
            }

            .top-tools {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .back-btn {
                display: inline-block;
                text-decoration: none;
                border: 1px solid rgba(237, 210, 154, 0.55);
                color: #f2e2c1;
                border-radius: 8px;
                padding: 7px 10px;
                font-size: 0.8rem;
                letter-spacing: 0.07em;
                text-transform: uppercase;
                background: linear-gradient(180deg, rgba(78, 95, 123, 0.65), rgba(47, 57, 74, 0.75));
            }

            .back-btn:hover {
                filter: brightness(1.08);
            }

            .subtitle {
                margin: 0 0 24px;
                color: var(--ink-soft);
                text-transform: uppercase;
                letter-spacing: 0.11em;
                font-size: 0.83rem;
                position: relative;
                z-index: 2;
            }

            .portals {
                position: relative;
                z-index: 2;
                display: grid;
                gap: 34px;
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
                align-items: end;
                justify-items: center;
            }

            .portal {
                width: min(340px, 92vw);
                min-height: 540px;
                display: grid;
                justify-items: center;
                align-content: start;
                gap: 16px;
                text-decoration: none;
                color: inherit;
                transition: transform 220ms ease, filter 220ms ease;
            }

            .portal.open:hover {
                transform: translateY(-7px) scale(1.013);
                filter: saturate(1.06);
            }

            .portal.open:hover .portal-door {
                filter: brightness(1.04);
            }

            .frame {
                width: 302px;
                height: 408px;
                border-radius: 156px 156px 14px 14px;
                position: relative;
                border: 2px solid rgba(42, 30, 16, 0.85);
                background:
                    radial-gradient(170px 90px at 50% 8%, rgba(255, 238, 204, 0.36), transparent 75%),
                    linear-gradient(145deg, var(--gold-1) 0%, var(--gold-2) 38%, #d9ba83 70%, #76552f 100%);
                box-shadow:
                    0 24px 44px rgba(0, 0, 0, 0.58),
                    inset 0 0 0 2px rgba(255, 241, 209, 0.43),
                    inset 0 0 0 12px rgba(53, 36, 18, 0.34);
                overflow: hidden;
            }

            .frame::before {
                content: "";
                position: absolute;
                inset: 12px;
                border-radius: 140px 140px 9px 9px;
                border: 1px solid rgba(255, 239, 208, 0.48);
                pointer-events: none;
                z-index: 1;
            }

            .portal-door {
                position: absolute;
                inset: 40px 44px 36px;
                border-radius: 122px 122px 6px 6px;
                border: 2px solid rgba(248, 237, 209, 0.4);
                overflow: hidden;
                box-shadow:
                    inset 0 0 0 2px rgba(50, 31, 15, 0.42),
                    inset 0 -26px 40px rgba(8, 9, 14, 0.33);
            }

            .portal.open .portal-door {
                background:
                    radial-gradient(170px 220px at 50% 24%, rgba(236, 222, 188, 0.34), rgba(148, 108, 58, 0.68) 56%, rgba(70, 46, 20, 0.95) 100%),
                    linear-gradient(180deg, #8f6c3e 0%, #684a27 100%);
            }

            .portal.open .portal-door::before {
                content: "";
                position: absolute;
                inset: 0;
                background:
                    repeating-radial-gradient(circle at 50% 55%, rgba(245, 228, 190, 0.18) 0 2px, transparent 2px 20px),
                    repeating-linear-gradient(90deg, rgba(255, 239, 201, 0.08) 0 1px, transparent 1px 24px),
                    repeating-linear-gradient(0deg, rgba(58, 37, 16, 0.08) 0 1px, transparent 1px 20px);
                opacity: 0.9;
                pointer-events: none;
                z-index: 1;
            }

            .portal.open .portal-door::after {
                content: "";
                position: absolute;
                left: 50%;
                top: 52%;
                width: 64%;
                height: 64%;
                transform: translate(-50%, -50%);
                border-radius: 50%;
                border: 1px solid rgba(248, 228, 182, 0.38);
                background:
                    conic-gradient(from 0deg, transparent 0 12%, rgba(245, 227, 185, 0.16) 12% 14%, transparent 14% 26%, rgba(245, 227, 185, 0.16) 26% 28%, transparent 28% 40%, rgba(245, 227, 185, 0.16) 40% 42%, transparent 42% 54%, rgba(245, 227, 185, 0.16) 54% 56%, transparent 56% 68%, rgba(245, 227, 185, 0.16) 68% 70%, transparent 70% 82%, rgba(245, 227, 185, 0.16) 82% 84%, transparent 84% 100%);
                box-shadow: inset 0 0 24px rgba(21, 13, 5, 0.22);
                pointer-events: none;
                z-index: 1;
            }

            .portal.condemned .portal-door {
                background:
                    radial-gradient(170px 220px at 50% 24%, rgba(132, 147, 172, 0.12), rgba(52, 61, 79, 0.9) 56%, rgba(22, 27, 38, 0.98) 100%),
                    linear-gradient(180deg, #4a5670 0%, #293344 100%);
                box-shadow:
                    inset 0 0 0 2px rgba(22, 28, 39, 0.78),
                    inset 0 -26px 40px rgba(3, 4, 8, 0.55);
            }

            .swirl {
                position: absolute;
                left: 50%;
                top: 54%;
                transform: translate(-50%, -50%) rotate(8deg);
                border-radius: 50%;
                border: 6px solid rgba(229, 245, 236, 0.84);
                width: 76%;
                height: 84%;
                filter: drop-shadow(0 0 6px rgba(220, 248, 234, 0.56));
                animation: breath 7.6s ease-in-out infinite;
            }

            .swirl.s2 {
                width: 61%;
                height: 67%;
                border-width: 5px;
                animation-delay: -1.2s;
            }

            .swirl.s3 {
                width: 42%;
                height: 48%;
                border-width: 4px;
                animation-delay: -2.4s;
            }

            .swirl.s4 {
                width: 25%;
                height: 30%;
                border-width: 3px;
                animation-delay: -3.6s;
            }

            .portal.condemned .swirl {
                border-color: rgba(215, 223, 236, 0.3);
                filter: none;
                animation: none;
            }

            .portal.condemned:hover {
                transform: translateY(-5px) scale(1.012);
                filter: saturate(1.06);
            }

            .portal.condemned:hover .portal-door {
                background:
                    radial-gradient(180px 230px at 50% 24%, rgba(210, 227, 255, 0.25), rgba(92, 112, 145, 0.78) 56%, rgba(34, 44, 62, 0.96) 100%),
                    linear-gradient(180deg, #687a9c 0%, #3f4f69 100%);
                box-shadow:
                    inset 0 0 0 2px rgba(192, 209, 242, 0.34),
                    inset 0 -26px 40px rgba(8, 12, 20, 0.38),
                    0 0 28px rgba(151, 181, 238, 0.35);
            }

            .portal.condemned:hover .swirl {
                border-color: rgba(209, 226, 255, 0.48);
            }

            .portal.condemned:hover .chain {
                box-shadow: 0 0 10px rgba(210, 227, 255, 0.36);
            }

            .portal.open .swirl {
                border-color: rgba(247, 224, 174, 0.78);
                filter: drop-shadow(0 0 5px rgba(255, 232, 184, 0.45));
            }

            .glass {
                position: absolute;
                inset: 0;
                pointer-events: none;
                background:
                    linear-gradient(112deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.03) 26%, transparent 44%),
                    radial-gradient(130px 88px at 24% 16%, rgba(255, 255, 255, 0.16), transparent 72%);
                z-index: 2;
            }

            .chain {
                position: absolute;
                left: 50%;
                top: 49%;
                width: 304px;
                height: 14px;
                transform: translate(-50%, -50%) rotate(-16deg);
                border-radius: 999px;
                background:
                    repeating-linear-gradient(90deg, rgba(242, 245, 251, 0.76) 0 16px, rgba(150, 161, 179, 0.88) 16px 25px);
                box-shadow: 0 0 7px rgba(255, 255, 255, 0.26);
                opacity: 0.84;
                z-index: 6;
            }

            .chain.c2 {
                top: 42.5%;
                transform: translate(-50%, -50%) rotate(18deg);
            }

            .chain.c3 {
                top: 61%;
                transform: translate(-50%, -50%) rotate(-30deg);
            }

            .chain.c4 {
                top: 71%;
                transform: translate(-50%, -50%) rotate(4deg);
            }

            .condemn-stripe {
                position: absolute;
                left: 50%;
                top: 54%;
                width: 294px;
                height: 37px;
                transform: translate(-50%, -50%) rotate(-12deg);
                border: 2px solid rgba(34, 28, 20, 0.72);
                background: repeating-linear-gradient(135deg, #f4d64a 0 18px, #1f1c18 18px 36px);
                box-shadow: 0 8px 14px rgba(0, 0, 0, 0.32);
                z-index: 8;
            }

            .condemn-stripe.s2 {
                top: 40%;
                transform: translate(-50%, -50%) rotate(16deg);
            }

            .condemn-seal {
                position: absolute;
                left: 50%;
                top: 56%;
                transform: translate(-50%, -50%) rotate(-5deg);
                padding: 8px 18px 9px;
                border-radius: 999px;
                border: 2px solid rgba(99, 23, 23, 0.95);
                background:
                    radial-gradient(80px 34px at 50% 30%, rgba(255, 193, 193, 0.28), transparent 66%),
                    var(--danger);
                color: #fff1f1;
                text-transform: uppercase;
                letter-spacing: 0.09em;
                font-size: 0.86rem;
                font-weight: 700;
                z-index: 9;
                box-shadow: 0 5px 12px rgba(0, 0, 0, 0.33);
            }

            .plinth {
                width: 292px;
                padding: 13px 14px 12px;
                border-radius: 6px;
                border: 1px solid rgba(255, 249, 235, 0.6);
                box-shadow: 0 14px 28px rgba(0, 0, 0, 0.35), inset 0 0 0 1px rgba(95, 74, 51, 0.33);
            }

            .portal.open .plinth {
                background:
                    linear-gradient(180deg, rgba(232, 220, 197, 0.96), rgba(198, 178, 147, 0.94)),
                    repeating-linear-gradient(120deg, rgba(128, 97, 56, 0.16) 0 10px, rgba(90, 66, 36, 0.08) 10px 20px);
            }

            .portal.condemned .plinth {
                background:
                    linear-gradient(180deg, rgba(214, 219, 229, 0.9), rgba(150, 160, 176, 0.9)),
                    repeating-linear-gradient(120deg, rgba(72, 84, 106, 0.14) 0 10px, rgba(56, 66, 84, 0.08) 10px 20px);
            }

            .portal-name {
                margin: 0;
                text-align: center;
                text-transform: uppercase;
                letter-spacing: 0.14em;
                font-size: clamp(1.06rem, 2.5vw, 1.42rem);
                font-family: "Cinzel", "Trajan Pro", "Times New Roman", serif;
                font-weight: 700;
                text-shadow: 0 1px 0 rgba(255, 251, 240, 0.44);
            }

            .portal.open .portal-name {
                color: #4b351c;
            }

            .portal.condemned .portal-name {
                color: #283348;
            }

            .portal-subtitle {
                margin-top: 5px;
                text-align: center;
                font-size: 0.78rem;
                text-transform: uppercase;
                letter-spacing: 0.09em;
                opacity: 0.84;
            }

            .portal.open .portal-subtitle {
                color: #6c5334;
            }

            .portal.condemned .portal-subtitle {
                color: #34435a;
            }

            .status-line {
                margin-top: 14px;
                text-align: center;
                text-transform: uppercase;
                font-size: 0.8rem;
                letter-spacing: 0.1em;
                color: rgba(237, 221, 188, 0.78);
                position: relative;
                z-index: 2;
            }

            .status-line strong {
                color: #f0deba;
            }

            .floor {
                position: absolute;
                left: 0;
                right: 0;
                bottom: -30px;
                height: 180px;
                background:
                    radial-gradient(900px 120px at 50% 10%, rgba(255, 234, 194, 0.12), transparent 70%),
                    linear-gradient(180deg, rgba(31, 25, 19, 0.4), rgba(9, 8, 11, 0.85));
                pointer-events: none;
                z-index: 1;
            }

            @keyframes breath {
                0% { transform: translate(-50%, -50%) rotate(8deg) scale(1); }
                50% { transform: translate(-50%, -50%) rotate(1deg) scale(1.02); }
                100% { transform: translate(-50%, -50%) rotate(8deg) scale(1); }
            }

            @media (max-width: 760px) {
                .panel {
                    padding: 24px 16px 22px;
                }

                .topline {
                    flex-direction: column;
                    align-items: flex-start;
                    margin-bottom: 14px;
                }

                .meta {
                    text-align: left;
                }

                .top-tools {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .portals {
                    gap: 22px;
                }

                .portal {
                    min-height: 500px;
                }

                .status-line {
                    font-size: 0.74rem;
                    letter-spacing: 0.08em;
                }
            }
        </style>
    </head>
    <body>
        <div class="museum-bg" aria-hidden="true"></div>
        <div class="ambience" aria-hidden="true"></div>

        <div class="wrap">
            <main class="panel">
                <div class="topline">
                    <h1>Les Portails</h1>
                    <div class="top-tools">
                        <a class="back-btn" href="{{ url('/') }}">Retour menu</a>
                        <div class="meta">Hall des accès | LoreRoom Museum</div>
                    </div>
                </div>
                <p class="subtitle">Sélectionne une aile active. Les sections condamnées restent verrouillées.</p>

                <div class="portals">
                    <a class="portal open" href="{{ url('/manage') }}" aria-label="Entrer dans le portail de gestion">
                        <div class="frame" aria-hidden="true">
                            <div class="portal-door">
                                <span class="swirl s1"></span>
                                <span class="swirl s2"></span>
                                <span class="swirl s3"></span>
                                <span class="swirl s4"></span>
                                <span class="glass"></span>
                            </div>
                        </div>
                        <div class="plinth">
                            <p class="portal-name">Gestion</p>
                            <div class="portal-subtitle">Archives et Chroniques</div>
                        </div>
                    </a>

                    <div class="portal condemned" role="img" aria-label="Portail condamne, hors service">
                        <div class="frame" aria-hidden="true">
                            <div class="portal-door">
                                <span class="swirl s1"></span>
                                <span class="swirl s2"></span>
                                <span class="swirl s3"></span>
                                <span class="swirl s4"></span>
                                <span class="chain c1"></span>
                                <span class="chain c2"></span>
                                <span class="chain c3"></span>
                                <span class="chain c4"></span>
                                <span class="condemn-stripe s1"></span>
                                <span class="condemn-stripe s2"></span>
                                <span class="condemn-seal">HORS SERVICE</span>
                                <span class="glass"></span>
                            </div>
                        </div>
                        
                    </div>
                </div>

            </main>
        </div>
    </body>
</html>
