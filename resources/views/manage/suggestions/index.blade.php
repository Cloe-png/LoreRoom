@extends('manage.layout')

@section('title', 'Gestion - Suggestions')
@section('header', 'Boite à suggestions')

@section('content')
    <style>
        .suggestions-page {
            display: grid;
            gap: 14px;
            position: relative;
            z-index: 3;
        }

        .suggestion-hero,
        .suggestion-panel {
            border: 1px solid rgba(114, 84, 49, .34);
            border-radius: 12px;
            background:
                linear-gradient(180deg, rgba(248, 237, 215, .98), rgba(235, 218, 186, .97)),
                repeating-linear-gradient(0deg, transparent 0 29px, rgba(73, 120, 177, .15) 29px 30px);
            box-shadow: 0 10px 24px rgba(70, 45, 19, .14);
            color: #322514;
            position: relative;
            overflow: hidden;
        }

        .suggestion-hero {
            padding: 18px 18px 16px;
        }

        .suggestion-hero::after {
            content: "";
            position: absolute;
            right: -28px;
            top: -30px;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(127, 94, 54, .16) 0%, rgba(127, 94, 54, 0) 72%);
        }

        .suggestion-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px;
            border-radius: 999px;
            border: 1px dashed rgba(104, 73, 40, .45);
            background: rgba(255, 246, 226, .52);
            color: #51371d;
            font-size: .78rem;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .suggestion-title {
            margin: 12px 0 6px;
            color: #3e2815;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: clamp(1.35rem, 2.5vw, 1.9rem);
            letter-spacing: .04em;
        }

        .suggestion-copy {
            margin: 0;
            max-width: 820px;
            color: #5f4630;
            line-height: 1.5;
        }

        .suggestion-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: minmax(0, 1.5fr) minmax(280px, .9fr);
            align-items: start;
        }

        .suggestion-panel {
            padding: 16px;
        }

        .suggestion-panel::before {
            content: "";
            position: absolute;
            left: 26px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(188, 72, 72, .28);
        }

        .suggestion-panel-inner {
            padding-left: 24px;
        }

        .suggestion-panel h2 {
            margin: 0 0 12px;
            color: #4a3117;
            font-family: "Segoe Print", "Comic Sans MS", cursive;
            font-size: 1.08rem;
        }

        .suggestion-meta {
            display: grid;
            gap: 10px;
        }

        .suggestion-note {
            margin: 0;
            color: #6d5234;
            font-size: .92rem;
            line-height: 1.45;
        }

        .suggestion-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .suggestion-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 999px;
            border: 1px solid rgba(114, 84, 49, .3);
            background: rgba(255, 255, 255, .45);
            color: #5c432a;
            font-size: .8rem;
        }

        .suggestion-checklist {
            margin: 0;
            padding-left: 18px;
            color: #5f4630;
        }

        .suggestion-checklist li + li {
            margin-top: 8px;
        }

        .suggestion-status {
            display: none;
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: .92rem;
        }

        .suggestion-status.is-visible {
            display: block;
        }

        .suggestion-status[data-tone="success"] {
            border: 1px solid rgba(83, 173, 120, .45);
            background: rgba(180, 236, 200, .55);
            color: #204a32;
        }

        .suggestion-status[data-tone="error"] {
            border: 1px solid rgba(169, 50, 50, .45);
            background: rgba(246, 206, 206, .62);
            color: #5c2121;
        }

        .suggestion-submit {
            min-width: 170px;
            cursor: pointer;
        }

        .suggestion-submit[disabled] {
            opacity: .7;
            cursor: progress;
        }

        .suggestion-help {
            margin-top: 10px;
            color: #70573a;
            font-size: .88rem;
        }

        .suggestion-help code {
            background: rgba(255,255,255,.45);
            padding: 1px 4px;
            border-radius: 4px;
        }

        .suggestion-panel.is-disabled {
            opacity: .78;
        }

        @media (max-width: 980px) {
            .suggestion-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="suggestions-page">
        <section class="suggestion-hero">
            <span class="suggestion-kicker">Canal communauté</span>
            <h2 class="suggestion-title">Vous avez une ou plusieurs idées à ajouter pour cette application ? Envoyez-moi un mail !</h2>
            <p class="suggestion-copy">
                Cet espace permet aux utilisateurs de proposer des modules, des ajustements d'ergonomie,
                ou des idées de contenu.
            </p>
        </section>

        <section class="suggestion-grid">
            <article class="suggestion-panel @if($formspreeEndpoint === '') is-disabled @endif">
                <div class="suggestion-panel-inner">
                    <h2>Formulaire de suggestion</h2>

                    <div class="suggestion-status" id="suggestion-status" data-tone="success" role="status" aria-live="polite"></div>

                    <form id="suggestion-form" @if($formspreeEndpoint !== '') action="{{ $formspreeEndpoint }}" @endif method="POST" novalidate>
                        <div class="field">
                            <label for="suggestion_name">Pseudo</label>
                            <input
                                id="suggestion_name"
                                type="text"
                                name="Pseudo"
                                maxlength="120"
                                value="{{ old('name', $defaultName) }}"
                                placeholder="Ton nom ou pseudo"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="suggestion_email">Email</label>
                            <input
                                id="suggestion_email"
                                type="email"
                                name="Email"
                                maxlength="180"
                                value="{{ old('email', $defaultEmail) }}"
                                placeholder="nom@exemple.com"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="suggestion_subject">Sujet</label>
                            <input
                                id="suggestion_subject"
                                type="text"
                                name="_subject"
                                maxlength="160"
                                value="{{ old('_subject', 'Nouvelle suggestion LoreRoom') }}"
                                placeholder="Ex: Ajouter un calendrier par monde"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="suggestion_category">Catégorie</label>
                            <select id="suggestion_category" name="Catégorie">
                                <option value="Fonctionnalité">Votre idée ?</option>
                                <option value="Contenu">Idée de contenu</option>
                                <option value="Bug">Bug</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>


                        <div class="field">
                            <label for="suggestion_message">Ta suggestion / Ton problème</label>
                            <textarea
                                id="suggestion_message"
                                name="Le message"
                                placeholder="Explique l'idée, le besoin et si possible le résultat attendu."
                                required
                            >{{ old('message') }}</textarea>
                        </div>

                        <div class="field">
                            <label for="suggestion_value">Pourquoi c'est utile ?</label>
                            <textarea
                                id="suggestion_value"
                                name="Pourquoi ?"
                                placeholder="Ex: Cela ferait gagner du temps ou rendrait les fiches plus claires."
                            >{{ old('value') }}</textarea>
                        </div>

                        <input type="text" name="_gotcha" tabindex="-1" autocomplete="off" style="position:absolute; left:-9999px;" aria-hidden="true">
                        <input type="hidden" name="_format" value="plain">

                        <div class="stack">
                            <button
                                type="submit"
                                class="btn suggestion-submit"
                                id="suggestion-submit"
                                @if($formspreeEndpoint === '') disabled @endif
                            >
                                Envoyer la suggestion
                            </button>
                        </div>
                    </form>

                </div>
            </article>

            <aside class="suggestion-panel">
                <div class="suggestion-panel-inner suggestion-meta">
                    <div>
                        <h2>Ce qui est envoyé</h2>
                        <p class="suggestion-note">
                            Le mail contient le nom, l'email, la catégorie, le monde concerné, le sujet et le détail
                            de la suggestion pour que tu puisses prioriser plus facilement.
                        </p>
                    </div>
                </div>
            </aside>
        </section>
    </div>

    <script>
        (function () {
            const form = document.getElementById('suggestion-form');
            const status = document.getElementById('suggestion-status');
            const submit = document.getElementById('suggestion-submit');

            if (!form || !status || !submit || !form.action) {
                return;
            }

            function showStatus(message, tone) {
                status.textContent = message;
                status.dataset.tone = tone;
                status.classList.add('is-visible');
            }

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                if (!form.reportValidity()) {
                    return;
                }

                submit.disabled = true;
                submit.textContent = 'Envoi en cours...';
                status.classList.remove('is-visible');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Erreur Formspree');
                    }

                    form.reset();
                    showStatus('Suggestion envoyée avec succès. Merci pour ton message ! Tu aura une réponse au plus vite.', 'success');
                } catch (error) {
                    showStatus('Impossible d\'envoyer la suggestion pour le moment. Vérifie la configuration Formspree et réessaie.', 'error');
                } finally {
                    submit.disabled = false;
                    submit.textContent = 'Envoyer la suggestion';
                }
            });
        })();
    </script>
@endsection
