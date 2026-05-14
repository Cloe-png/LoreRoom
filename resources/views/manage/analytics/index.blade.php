@extends('manage.layout')

@section('title', 'Gestion - Analytics')
@section('header', 'Analytics')

@section('content')
    <style>
        .analytics-shell {
            display: grid;
            gap: 14px;
        }

        .analytics-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(114, 84, 49, .34);
            border-radius: 14px;
            padding: 20px 18px;
            background:
                radial-gradient(580px 210px at 8% -10%, rgba(255, 235, 194, .82), transparent 62%),
                linear-gradient(180deg, rgba(248, 237, 215, .97), rgba(230, 210, 173, .95));
            box-shadow: 0 16px 34px rgba(67, 45, 20, .16);
        }

        .analytics-hero::after {
            content: "";
            position: absolute;
            right: -34px;
            top: -48px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(194, 147, 86, .22), rgba(194, 147, 86, 0));
            pointer-events: none;
        }

        .analytics-hero-head {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .analytics-title {
            margin: 0;
            color: #4a3117;
            font-family: "Cinzel", "Times New Roman", serif;
            letter-spacing: .04em;
            font-size: clamp(1.25rem, 2vw, 1.8rem);
        }

        .analytics-text {
            margin: 8px 0 0;
            color: #6b5031;
            max-width: 760px;
            line-height: 1.5;
        }

        .analytics-range {
            border: 1px dashed rgba(104, 73, 40, .45);
            border-radius: 999px;
            background: rgba(255, 246, 226, .58);
            color: #51371d;
            padding: 7px 12px;
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
        }

        .analytics-filter-panel {
            padding: 16px;
        }

        .analytics-filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: end;
        }

        .analytics-filter-field {
            min-width: 220px;
            margin: 0;
        }

        .analytics-inline-form {
            display: flex;
            gap: 10px;
            align-items: end;
            flex-wrap: wrap;
            padding: 16px;
        }

        .analytics-inline-form .field {
            margin: 0;
            min-width: 240px;
            flex: 1 1 320px;
        }

        .analytics-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .analytics-card {
            border: 1px solid rgba(109, 78, 45, .24);
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 10px 22px rgba(50, 34, 17, .1);
            min-height: 120px;
        }

        .analytics-card:nth-child(1) { background: linear-gradient(180deg, #f8dfa3, #ebc271); }
        .analytics-card:nth-child(2) { background: linear-gradient(180deg, #c6e2d5, #9dc9b7); }
        .analytics-card:nth-child(3) { background: linear-gradient(180deg, #c6d7ef, #a5bee2); }
        .analytics-card:nth-child(4) { background: linear-gradient(180deg, #e8c8c8, #dca6a6); }
        .analytics-card:nth-child(5) { background: linear-gradient(180deg, #ead9b7, #d7bc8a); }
        .analytics-card:nth-child(6) { background: linear-gradient(180deg, #d7e0bf, #b7c988); }
        .analytics-card:nth-child(7) { background: linear-gradient(180deg, #f0d6bc, #ddb08f); }
        .analytics-card:nth-child(8) { background: linear-gradient(180deg, #d7d0ea, #b8acd9); }

        .analytics-card-label {
            color: #5f4628;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .analytics-card-value {
            margin-top: 8px;
            color: #2e2113;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 2rem;
            line-height: 1;
        }

        .analytics-card-note {
            margin-top: 10px;
            color: #594129;
            font-size: .9rem;
            line-height: 1.35;
        }

        .analytics-columns {
            display: grid;
            gap: 12px;
            grid-template-columns: 1.2fr 1fr;
        }

        .analytics-panel {
            padding: 0;
            overflow: hidden;
            border-radius: 14px;
        }

        .analytics-panel-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 14px 16px 12px;
            border-bottom: 1px solid rgba(114, 84, 49, .18);
            background: linear-gradient(180deg, rgba(255,255,255,.24), rgba(255,255,255,.1));
        }

        .analytics-panel-title {
            margin: 0;
            color: #56391b;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1rem;
            letter-spacing: .04em;
        }

        .analytics-panel-meta {
            color: #75583a;
            font-size: .86rem;
        }

        .analytics-table-wrap {
            overflow-x: auto;
            padding: 0 16px 12px;
        }

        .analytics-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 520px;
        }

        .analytics-table thead th {
            background: rgba(241, 228, 201, .96);
            box-shadow: inset 0 -1px 0 rgba(114, 84, 49, .16);
        }

        .analytics-table td,
        .analytics-table th {
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }

        .analytics-table tbody tr:hover {
            background: rgba(255,255,255,.16);
        }

        .analytics-strong {
            color: #3f2a17;
            font-weight: 700;
        }

        .analytics-soft {
            color: #6a4d30;
            word-break: break-word;
        }

        .analytics-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: .76rem;
            letter-spacing: .05em;
            text-transform: uppercase;
            border: 1px solid rgba(95, 69, 37, .2);
            background: rgba(255,255,255,.54);
            color: #6e512f;
        }

        .analytics-empty {
            padding: 18px 16px 20px;
        }

        @media (max-width: 1180px) {
            .analytics-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .analytics-columns {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .analytics-grid {
                grid-template-columns: 1fr;
            }

            .analytics-filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .analytics-filter-form .btn {
                width: 100%;
                text-align: center;
            }

            .analytics-inline-form {
                flex-direction: column;
                align-items: stretch;
            }

            .analytics-inline-form .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>

    <div class="analytics-shell">
        <section class="analytics-hero">
            <div class="analytics-hero-head">
                <div>
                    <h2 class="analytics-title">Activité et sécurité</h2>
                    <p class="analytics-text">
                        Lecture rapide des pages consultées, des actions récentes, des connexions et des activités suspectes.
                    </p>
                </div>
                <div class="analytics-range">Depuis le {{ $since->format('d/m/Y') }}</div>
            </div>
        </section>

        <section class="panel analytics-filter-panel">
            <form method="GET" action="{{ route('manage.analytics.index') }}" class="analytics-filter-form">
                <div class="field analytics-filter-field">
                    <label>Période analysée</label>
                    <select name="days">
                        <option value="7" {{ $days === 7 ? 'selected' : '' }}>7 jours</option>
                        <option value="14" {{ $days === 14 ? 'selected' : '' }}>14 jours</option>
                        <option value="30" {{ $days === 30 ? 'selected' : '' }}>30 jours</option>
                        <option value="60" {{ $days === 60 ? 'selected' : '' }}>60 jours</option>
                        <option value="90" {{ $days === 90 ? 'selected' : '' }}>90 jours</option>
                    </select>
                </div>
                <button class="btn" type="submit">Mettre à jour</button>
            </form>
        </section>

        <section class="analytics-grid">
            <article class="analytics-card">
                <div class="analytics-card-label">Utilisateurs</div>
                <div class="analytics-card-value">{{ $overview['totalUsers'] }}</div>
                <div class="analytics-card-note">Nombre total d'utilisateurs inscrits.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Nouveaux aujourd'hui</div>
                <div class="analytics-card-value">{{ $overview['newUsersToday'] }}</div>
                <div class="analytics-card-note">Comptes créés aujourd'hui.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Nouveaux cette semaine</div>
                <div class="analytics-card-value">{{ $overview['newUsersWeek'] }}</div>
                <div class="analytics-card-note">Comptes créés depuis le debut de semaine.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Utilisateurs actifs</div>
                <div class="analytics-card-value">{{ $overview['activeUsers'] }}</div>
                <div class="analytics-card-note">Utilisateurs distincts actifs sur la période.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Connexions</div>
                <div class="analytics-card-value">{{ $overview['logins'] }}</div>
                <div class="analytics-card-note">Connexions réussies sur la période.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Actions realisées</div>
                <div class="analytics-card-value">{{ $overview['totalActions'] }}</div>
                <div class="analytics-card-note">Toutes les actions loguées sur la période.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Taux d'erreur</div>
                <div class="analytics-card-value">{{ number_format($overview['errorRate'], 1, ',', ' ') }}%</div>
                <div class="analytics-card-note">Base sur les échecs de connexion détectés.</div>
            </article>
            <article class="analytics-card">
                <div class="analytics-card-label">Temps moyen passé</div>
                <div class="analytics-card-value">{{ $overview['averageTimeSpent'] }}</div>
                <div class="analytics-card-note">Estimation par session avec coupure après 30 min d'inactivité.</div>
            </article>
        </section>

        <section class="analytics-columns">
            <section class="panel analytics-panel">
                <div class="analytics-panel-top">
                    <h3 class="analytics-panel-title">Top pages</h3>
                    <div class="analytics-panel-meta">Nom de page et nombre de consultations</div>
                </div>

                @if($topPages->isEmpty())
                    <div class="analytics-empty">
                        <p class="muted" style="margin:0;">Aucune consultation enregistrée sur cette période.</p>
                    </div>
                @else
                    <div class="analytics-table-wrap">
                        <table class="analytics-table">
                            <thead>
                                <tr>
                                    <th>Page</th>
                                    <th>Consultations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPages as $page)
                                    <tr>
                                        <td><div class="analytics-strong">{{ $page->route_name ?: 'Sans nom' }}</div></td>
                                        <td><div class="analytics-strong">{{ $page->consultations_count }}</div></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section class="panel analytics-panel">
                <div class="analytics-panel-top">
                    <h3 class="analytics-panel-title">Activités suspectes</h3>
                    <div class="analytics-panel-meta">{{ $overview['failedActions'] }} échec(s) de connexion sur la période</div>
                </div>

                @if($suspiciousActivities->isEmpty() && $lockedUsers->isEmpty())
                    <div class="analytics-empty">
                        <p class="muted" style="margin:0;">Aucun comportement suspect détecté sur cette période.</p>
                    </div>
                @else
                    <div class="analytics-table-wrap">
                        <table class="analytics-table">
                            <thead>
                                <tr>
                                    <th>Compte</th>
                                    <th>Signal</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lockedUsers as $user)
                                    <tr>
                                        <td>
                                            <div class="analytics-strong">{{ $user->name }}</div>
                                            <div class="analytics-soft">{{ $user->email }}</div>
                                        </td>
                                        <td><span class="analytics-badge">Compte bloqué</span></td>
                                        <td>
                                            <div class="analytics-strong">{{ $user->failed_login_attempts }} tentative(s) échouée(s)</div>
                                            <div class="analytics-soft">Bloqué le {{ optional($user->locked_at)->format('d/m/Y H:i') ?: '-' }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach($suspiciousActivities as $entry)
                                    <tr>
                                        <td>
                                            <div class="analytics-strong">{{ optional($entry->user)->name ?: 'Utilisateur inconnu' }}</div>
                                            <div class="analytics-soft">{{ optional($entry->user)->email ?: '-' }}</div>
                                        </td>
                                        <td><span class="analytics-badge">Tentatives répétées</span></td>
                                        <td>
                                            <div class="analytics-strong">{{ $entry->failed_attempts }} échec(s)</div>
                                            <div class="analytics-soft">Dernière tentative le {{ $entry->last_attempt_at ? \Carbon\Carbon::parse($entry->last_attempt_at)->format('d/m/Y H:i') : '-' }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </section>

        <section class="panel analytics-panel">
            <div class="analytics-panel-top">
                <h3 class="analytics-panel-title">Dernières actions par utilisateur</h3>
                <div class="analytics-panel-meta">
                    {{ $userSearch !== '' ? 'Recherche : ' . $userSearch : '10 actions récentes maximum' }}
                </div>
            </div>

            <form method="GET" action="{{ route('manage.analytics.index') }}" class="analytics-inline-form">
                <input type="hidden" name="days" value="{{ $days }}">
                <div class="field">
                    <label>Rechercher un utilisateur</label>
                    <input
                        type="text"
                        name="user_search"
                        value="{{ $userSearch }}"
                        placeholder="Pseudo ou email"
                    >
                </div>
                <button class="btn" type="submit">Rechercher</button>
                @if($userSearch !== '')
                    <a class="btn secondary" href="{{ route('manage.analytics.index', ['days' => $days]) }}">Effacer</a>
                @endif
            </form>

            @if($recentActions->isEmpty())
                <div class="analytics-empty">
                    <p class="muted" style="margin:0;">Aucune action ne correspond à cette recherche.</p>
                </div>
            @else
                <div class="analytics-table-wrap">
                    <table class="analytics-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Utilisateur</th>
                                <th>Action</th>
                                <th>Contexte</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentActions as $log)
                                <tr>
                                    <td><div class="analytics-strong">{{ optional($log->action_at)->format('d/m/Y H:i') }}</div></td>
                                    <td>
                                        <div class="analytics-strong">{{ optional($log->user)->name ?: 'Utilisateur supprimé' }}</div>
                                        <div class="analytics-soft">{{ optional($log->user)->email ?: '-' }}</div>
                                    </td>
                                    <td><span class="analytics-badge">{{ $log->action_label }}</span></td>
                                    <td>
                                        <div class="analytics-soft">{{ $log->route_name ?: '-' }}</div>
                                        <div class="analytics-soft">{{ $log->route_path ?: '-' }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
