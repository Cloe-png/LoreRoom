@extends('manage.layout')

@section('title', __('app.account.page_title'))
@section('header', __('app.nav.settings'))

@section('content')
    <style>
        .account-shell {
            display: grid;
            gap: 14px;
            max-width: 920px;
        }
        .account-hero {
            border: 1px solid rgba(114, 84, 49, .35);
            border-radius: 12px;
            padding: 16px 18px 15px;
            background:
                radial-gradient(560px 200px at 8% -10%, rgba(255, 239, 208, .82), transparent 62%),
                linear-gradient(180deg, rgba(249, 238, 216, .98), rgba(236, 220, 189, .97));
            box-shadow: 0 8px 18px rgba(70, 45, 19, .12);
        }
        .account-title {
            margin: 0;
            color: #6a4724;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: clamp(1.2rem, 2vw, 1.55rem);
            letter-spacing: .03em;
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
        .role-section {
            margin: 0 0 16px;
            border: 1px solid rgba(114, 84, 49, .28);
            border-radius: 12px;
            background: rgba(255,255,255,.18);
            padding: 14px 14px 13px;
        }
        .role-section legend {
            padding: 0 6px;
            color: #6a4724;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: .98rem;
            letter-spacing: .03em;
        }
        .role-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 12px;
            margin-top: 10px;
        }
        .role-card {
            display: grid;
            grid-template-columns: 22px 1fr;
            gap: 2px 10px;
            align-content: start;
            border-radius: 10px;
            border: 1px solid rgba(108,77,43,.22);
            background:
                linear-gradient(180deg, rgba(255,255,255,.32), rgba(243,228,196,.58));
            padding: 12px 12px 11px;
            cursor: pointer;
        }
        .role-card input {
            margin: 2px 0 0;
            width: 18px;
            height: 18px;
            accent-color: #8a6135;
        }
        .role-card strong {
            display: block;
            color: #2f2013;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1rem;
            letter-spacing: .02em;
        }
        .role-card span {
            color: #6f5841;
            font-size: .88rem;
            line-height: 1.35;
            grid-column: 2;
        }
        .account-security {
            margin-top: 12px;
            border: 1px solid rgba(114, 84, 49, .22);
            border-radius: 12px;
            background: rgba(255,255,255,.18);
            padding: 14px 14px 12px;
        }
        .account-security-title {
            margin: 0 0 8px;
            color: #6a4724;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: .98rem;
            letter-spacing: .03em;
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
            content: "*";
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
            content: "OK";
            border-color: rgba(73, 145, 99, .42);
            background: rgba(184, 236, 198, .7);
            color: #245035;
            font-size: .56rem;
        }
    </style>

    <div class="account-shell">
        <section class="account-hero">
            <h2 class="account-title">{{ __('app.account.password_title') }}</h2>
            <p class="account-text">{{ __('app.account.password_intro') }}</p>
        </section>

        <section class="panel account-panel">
            <form method="POST" action="{{ route('manage.account.locale') }}" style="margin-bottom:16px;">
                @csrf
                @method('PUT')

                <div class="field">
                    <label for="locale">{{ __('app.account.language_label') }}</label>
                    <select id="locale" name="locale">
                        @foreach(($localeOptions ?? []) as $localeCode => $localeLabel)
                            <option value="{{ $localeCode }}" {{ old('locale', $user->preferred_locale ?? app()->getLocale()) === $localeCode ? 'selected' : '' }}>
                                {{ $localeLabel }}
                            </option>
                        @endforeach
                    </select>
                    <div class="account-help">{{ __('app.account.language_help') }}</div>
                </div>

                <button class="btn secondary" type="submit">{{ __('app.account.language_submit') }}</button>
            </form>

            <form method="POST" action="{{ route('manage.account.roles') }}" style="margin-bottom:16px;">
                @csrf
                @method('PUT')

                <fieldset class="role-section">
                    <legend>{{ __('app.account_roles.title') }}</legend>
                    <p class="account-text" style="margin:0;">{{ __('app.account_roles.help') }}</p>
                    <div class="role-grid">
                        @php
                            $selectedRoles = old('roles', $user->roles ?? []);
                        @endphp
                        @foreach(($roleOptions ?? []) as $roleKey => $roleInfo)
                            <label class="role-card">
                                <input type="checkbox" name="roles[]" value="{{ $roleKey }}" {{ in_array($roleKey, $selectedRoles, true) ? 'checked' : '' }}>
                                <strong>{{ __($roleInfo['label']) }}</strong>
                                <span>{{ __($roleInfo['help']) }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <button class="btn" type="submit">{{ __('app.account_roles.submit') }}</button>
            </form>

            @if(!empty($activeWorld))
                <div class="account-security" style="margin-bottom:16px;">
                    <h3 class="account-security-title">{{ __('app.account.export_title') }}</h3>
                    <p class="account-security-text">{{ __('app.account.export_help', ['world' => $activeWorld->name]) }}</p>
                    <a class="btn" href="{{ route('manage.account.export-world') }}">{{ __('app.account.export_submit') }}</a>
                </div>
            @endif

            <div class="account-security" style="margin-bottom:16px;">
                <h3 class="account-security-title">{{ __('app.account.import_title') }}</h3>
                <p class="account-security-text">{{ __('app.account.import_help') }}</p>
                <form method="POST" action="{{ route('manage.account.import-world') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="field">
                        <label for="world_archive">{{ __('app.account.import_file_label') }}</label>
                        <input id="world_archive" type="file" name="world_archive" accept=".zip,.json" required>
                    </div>
                    <button class="btn secondary" type="submit">{{ __('app.account.import_submit') }}</button>
                </form>
            </div>

            <form method="POST" action="{{ route('manage.account.password') }}">
                @csrf
                @method('PUT')

                <div class="field">
                    <label for="current_password">{{ __('app.account.current_password') }}</label>
                    <input id="current_password" type="password" name="current_password" autocomplete="current-password" required>
                </div>

                <div class="field">
                    <label for="password">{{ __('app.account.new_password') }}</label>
                    <input id="password" type="password" name="password" autocomplete="new-password" minlength="12" required>
                    <div class="account-help">{{ $passwordHelp }}</div>

                    <div class="account-security" aria-live="polite">
                        <h3 class="account-security-title">{{ __('app.account.security_title') }}</h3>
                        <ul class="account-checklist">
                            <li class="account-check" data-rule="length">{{ __('app.account.security_length') }}</li>
                            <li class="account-check" data-rule="lower">{{ __('app.account.security_lower') }}</li>
                            <li class="account-check" data-rule="upper">{{ __('app.account.security_upper') }}</li>
                            <li class="account-check" data-rule="digit">{{ __('app.account.security_digit') }}</li>
                            <li class="account-check" data-rule="special">{{ __('app.account.security_special') }}</li>
                        </ul>
                    </div>
                </div>

                <div class="field">
                    <label for="password_confirmation">{{ __('app.account.confirm_password') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" minlength="12" required>
                </div>

                <button class="btn" type="submit">{{ __('app.account.password_submit') }}</button>
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




