@extends('manage.layout')

@section('title', 'Gestion - Paramètres')
@section('header', 'Paramètres')

@section('content')
    <style>
        .account-shell { display: grid; gap: 14px; }
        .account-hero {
            border: 1px solid rgba(114, 84, 49, .34);
            border-radius: 14px;
            padding: 20px 18px;
            background:
                radial-gradient(560px 200px at 8% -10%, rgba(255, 234, 196, .82), transparent 62%),
                linear-gradient(180deg, rgba(248, 237, 215, .97), rgba(228, 208, 176, .95));
            box-shadow: 0 16px 34px rgba(67, 45, 20, .16);
        }
        .account-title {
            margin: 0;
            color: #4a3117;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: clamp(1.25rem, 2vw, 1.8rem);
            letter-spacing: .04em;
        }
        .account-text {
            margin: 8px 0 0;
            color: #6b5031;
            max-width: 780px;
            line-height: 1.5;
        }
        .account-panel {
            max-width: 760px;
        }
        .account-help {
            margin-top: 6px;
            color: #6a4d30;
            font-size: .9rem;
        }
        .account-security {
            margin-top: 12px;
            border: 1px solid rgba(114, 84, 49, .22);
            border-radius: 12px;
            background: rgba(255,255,255,.28);
            padding: 14px 14px 12px;
        }
        .account-security-title {
            margin: 0 0 8px;
            color: #56391b;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1rem;
            letter-spacing: .04em;
        }
        .account-security-text {
            margin: 0 0 10px;
            color: #6a4d30;
            font-size: .92rem;
            line-height: 1.45;
        }
        .account-checklist {
            display: grid;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .account-check {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #6b5031;
            font-size: .92rem;
        }
        .account-check::before {
            content: "•";
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(114, 84, 49, .28);
            background: rgba(255,255,255,.5);
            color: #7a5c3b;
            font-size: .8rem;
            flex: 0 0 18px;
        }
        .account-check.is-valid {
            color: #245035;
            font-weight: 600;
        }
        .account-check.is-valid::before {
            content: "✓";
            border-color: rgba(73, 145, 99, .42);
            background: rgba(184, 236, 198, .7);
            color: #245035;
        }
    </style>

    <div class="account-shell">
        <section class="account-hero">
            <h2 class="account-title">Changer mon mot de passe</h2>
            <p class="account-text">
                Mets à jour ton mot de passe ici.
            </p>
        </section>

        <section class="panel account-panel">
            <form method="POST" action="{{ route('manage.account.password') }}">
                @csrf
                @method('PUT')

                <div class="field">
                    <label for="current_password">Mot de passe actuel</label>
                    <input id="current_password" type="password" name="current_password" autocomplete="current-password" required>
                </div>

                <div class="field">
                    <label for="password">Nouveau mot de passe</label>
                    <input id="password" type="password" name="password" autocomplete="new-password" minlength="12" required>
                    <div class="account-help">{{ $passwordHelp }}</div>

                    <div class="account-security" aria-live="polite">
                        <h3 class="account-security-title">Checklist sécurité</h3>
                        <ul class="account-checklist">
                            <li class="account-check" data-rule="length">12 caractéres minimum</li>
                            <li class="account-check" data-rule="lower">Au moins 1 minuscule</li>
                            <li class="account-check" data-rule="upper">Au moins 1 majuscule</li>
                            <li class="account-check" data-rule="digit">Au moins 1 chiffre</li>
                            <li class="account-check" data-rule="special">Au moins 1 caractére spécial</li>
                        </ul>
                    </div>
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirmation du nouveau mot de passe</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="12" required>
                </div>

                <button class="btn" type="submit">Mettre à jour mon mot de passe</button>
            </form>
        </section>
    </div>

    <script>
        (function () {
            const input = document.getElementById('password');
            if (!input) return;

            const checks = {
                length: function (value) { return value.length >= 12; },
                lower: function (value) { return /[a-z]/.test(value); },
                upper: function (value) { return /[A-Z]/.test(value); },
                digit: function (value) { return /[0-9]/.test(value); },
                special: function (value) { return /[^A-Za-z0-9]/.test(value); }
            };

            const items = Array.from(document.querySelectorAll('.account-check[data-rule]'));

            function refreshChecklist() {
                const value = input.value || '';

                items.forEach(function (item) {
                    const rule = item.getAttribute('data-rule');
                    const isValid = checks[rule] ? checks[rule](value) : false;
                    item.classList.toggle('is-valid', isValid);
                });
            }

            input.addEventListener('input', refreshChecklist);
            refreshChecklist();
        })();
    </script>
@endsection
