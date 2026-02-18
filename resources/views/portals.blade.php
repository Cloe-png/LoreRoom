<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LoreRoom - Connexion</title>
    <style>
        :root {
            --ink: #2d2014;
            --ink-soft: #6b5338;
            --paper: #efe3cd;
            --gold: #cb9a52;
            --gold-deep: #8f6533;
            --night-1: #101826;
            --night-2: #22324e;
            --danger-bg: #f8dddd;
            --danger-border: #b45a5a;
            --danger-text: #6a1f1f;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Garamond", "Georgia", serif;
            color: var(--ink);
            background:
                radial-gradient(1000px 420px at 50% -12%, rgba(255, 231, 188, .34), transparent 72%),
                linear-gradient(180deg, var(--night-2), var(--night-1));
            display: grid;
            place-items: center;
            padding: 20px;
        }

        .bg {
            position: fixed;
            inset: 0;
            background-image: url('{{ asset('museum_background.jpg') }}');
            background-size: cover;
            background-position: center;
            opacity: .16;
            filter: blur(4px) saturate(.88);
            z-index: 0;
        }

        .shell {
            width: min(920px, 96vw);
            border-radius: 18px;
            border: 1px solid rgba(212, 176, 117, .55);
            background: linear-gradient(160deg, rgba(244, 233, 210, .96), rgba(233, 219, 192, .95));
            box-shadow: 0 28px 96px rgba(0,0,0,.46);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            position: relative;
            z-index: 1;
        }

        .hero {
            padding: 30px 26px;
            color: #f3ead8;
            background:
                radial-gradient(280px 160px at 20% 20%, rgba(243, 214, 164, .24), transparent 72%),
                linear-gradient(180deg, rgba(41,57,87,.96), rgba(22,30,44,.98));
            border-right: 1px solid rgba(215, 179, 120, .22);
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(1.9rem, 4.4vw, 2.6rem);
            letter-spacing: .03em;
            font-family: "Cinzel", "Times New Roman", serif;
        }

        .hero p {
            margin: 10px 0 0;
            color: #dccfb7;
            line-height: 1.45;
            font-size: .97rem;
        }

        .badge {
            margin-top: 18px;
            display: inline-block;
            padding: 7px 11px;
            border-radius: 999px;
            border: 1px solid rgba(226, 197, 145, .42);
            background: rgba(255,255,255,.08);
            text-transform: uppercase;
            letter-spacing: .1em;
            font-size: .72rem;
            color: #f1e6cf;
        }

        .panel {
            padding: 30px 24px;
        }

        .head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 14px;
        }

        .head h2 {
            margin: 0;
            color: #3f2c16;
            font-size: 1.72rem;
            letter-spacing: .03em;
            font-family: "Cinzel", "Times New Roman", serif;
        }

        .meta {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: #755a3a;
            text-align: right;
            margin-top: 4px;
        }

        .muted {
            margin: 0 0 15px;
            color: var(--ink-soft);
            font-size: .95rem;
        }

        .alert {
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 14px;
            border: 1px solid var(--danger-border);
            background: var(--danger-bg);
            color: var(--danger-text);
            font-size: .92rem;
        }

        .alert.ok {
            border-color: #6f9464;
            background: #e4f1df;
            color: #2f5c23;
        }

        .field { margin-bottom: 11px; }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 700;
            font-size: .9rem;
            color: #3a2a18;
            letter-spacing: .03em;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            border: 1px solid rgba(108,77,43,.38);
            border-radius: 10px;
            padding: 11px 12px;
            background: #fffdf8;
            font: inherit;
            color: #2a1d12;
        }

        input:focus {
            outline: 2px solid rgba(177,126,65,.44);
            outline-offset: 1px;
        }

        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 6px 0 14px;
        }

        .check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: .9rem;
            color: #4a3722;
        }

        .actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn {
            border: 1px solid rgba(113,78,42,.4);
            border-radius: 10px;
            padding: 10px 14px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn.primary {
            color: #2f1f0f;
            background: linear-gradient(180deg, #e6bd76, #c9984d);
            min-width: 140px;
            text-align: center;
        }

        .btn.secondary {
            color: #e7dcc8;
            background: linear-gradient(180deg, #4b5f85, #354462);
            border-color: rgba(38,51,73,.56);
        }

        .help {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed rgba(93,65,34,.24);
            font-size: .86rem;
            color: #6f5841;
        }

        .help a {
            color: #73481d;
            font-weight: 700;
        }

        @media (max-width: 860px) {
            .shell { grid-template-columns: 1fr; }
            .hero { border-right: 0; border-bottom: 1px solid rgba(215, 179, 120, .22); }
        }
    </style>
</head>
<body>
    <div class="bg" aria-hidden="true"></div>

    <main class="shell">
        <aside class="hero">
            <h1>LoreRoom</h1>
            <p>Accedez au musee narratif pour gerer mondes, personnages, frises et relations.</p>
            <span class="badge">Portail de connexion</span>
        </aside>

        <section class="panel">
            <div class="head">
                <h2>Connexion</h2>
                <div class="meta">Espace gestion</div>
            </div>

            <p class="muted">Entrez vos identifiants pour ouvrir la session.</p>

            @if(session('success'))
                <div class="alert ok">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required>
                </div>

                <div class="field">
                    <label for="password">Mot de passe</label>
                    <input id="password" type="password" name="password" autocomplete="current-password" required>
                </div>

                <div class="row">
                    <label class="check" for="remember">
                        <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        Se souvenir de moi
                    </label>
                </div>

                <div class="actions">
                    <button class="btn primary" type="submit">Se connecter</button>
                    <a class="btn secondary" href="{{ url('/') }}">Retour menu</a>
                </div>
            </form>

            <div class="help">
                Nouveau sur LoreRoom ? <a href="{{ route('register') }}">Creer un compte</a>
            </div>
        </section>
    </main>
</body>
</html>
