<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LoreRoom - Inscription</title>
    <style>
        :root {
            --ink: #2d2014;
            --ink-soft: #6b5338;
            --night-1: #101826;
            --night-2: #22324e;
            --danger-bg: #f8dddd;
            --danger-border: #b45a5a;
            --danger-text: #6a1f1f;
            --ok-bg: #e4f1df;
            --ok-border: #6f9464;
            --ok-text: #2f5c23;
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
            width: min(640px, 96vw);
            border-radius: 18px;
            border: 1px solid rgba(212, 176, 117, .55);
            background: linear-gradient(160deg, rgba(244, 233, 210, .96), rgba(233, 219, 192, .95));
            box-shadow: 0 28px 96px rgba(0,0,0,.46);
            overflow: hidden;
            position: relative;
            z-index: 1;
            padding: 28px 24px;
        }

        .head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 14px;
        }

        h1 {
            margin: 0;
            color: #3f2c16;
            font-size: 1.86rem;
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
            border-color: var(--ok-border);
            background: var(--ok-bg);
            color: var(--ok-text);
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 11px;
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

        input[type="text"],
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

        .actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 4px;
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
            min-width: 160px;
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

        @media (max-width: 640px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="bg" aria-hidden="true"></div>

    <main class="shell">
        <div class="head">
            <h1>Inscription</h1>
            <div class="meta">Nouveau compte</div>
        </div>

        <p class="muted">Creez un compte LoreRoom. Votre role initial sera <strong>utilisateur</strong>.</p>

        @if(session('success'))
            <div class="alert ok">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <div class="field">
                <label for="name">Nom complet</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            </div>

            <div class="grid">
                <div class="field">
                    <label for="password">Mot de passe</label>
                    <input id="password" type="password" name="password" autocomplete="new-password" required>
                </div>
                <div class="field">
                    <label for="password_confirmation">Confirmation</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
                </div>
            </div>

            <div class="actions">
                <button class="btn primary" type="submit">Creer le compte</button>
                <a class="btn secondary" href="{{ route('login') }}">Deja inscrit ? Connexion</a>
            </div>
        </form>

        <div class="help">
            En validant, vous accedez directement au panneau de gestion.
        </div>
    </main>
</body>
</html>
