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
            width: min(560px, 96vw);
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

        .field { margin-bottom: 11px; }

        .field label {
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

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 12px 0 16px;
            font-size: .92rem;
            color: #4b3720;
            font-weight: 600;
            letter-spacing: .01em;
        }

        .remember input { width: auto; margin: 0; accent-color: #9a6a2f; }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            appearance: none;
            border: 1px solid rgba(102,72,36,.52);
            border-radius: 11px;
            background: linear-gradient(180deg, #f2c678, #e5ae54);
            color: #2d1f10;
            padding: 10px 14px;
            font-weight: 700;
            letter-spacing: .03em;
            text-decoration: none;
            cursor: pointer;
        }

        .btn.secondary {
            background: rgba(255,255,255,.58);
            color: #3b2a17;
        }

        .btn:hover { transform: translateY(-1px); }
        @media (max-width: 560px) {
            .shell {
                padding: 20px 16px;
                border-radius: 14px;
            }
            .actions {
                flex-direction: column;
            }
            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="bg"></div>

    <main class="shell">
        <div class="head">
            <h1>Connexion</h1>
            <div class="meta">LoreRoom</div>
        </div>
        <p class="muted">Accès sécurisé avec token temporaire de session.</p>

        @if(session('success'))
            <div class="alert ok">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
            </div>

            <div class="field">
                <label>Mot de passe</label>
                <input type="password" name="password" required autocomplete="current-password">
            </div>

            <label class="remember">
                <input type="checkbox" name="remember" value="1"> Se souvenir de moi
            </label>

            <div class="actions">
                <button class="btn" type="submit">Se connecter</button>
                <a class="btn secondary" href="{{ route('register') }}">Créer un compte</a>
            </div>
        </form>
    </main>
</body>
</html>

