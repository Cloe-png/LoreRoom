@extends('manage.layout')

@section('title', 'Gestion - Comptes')
@section('header', 'Comptes utilisateurs')

@section('content')
    <style>
        .users-admin-shell {
            display: grid;
            gap: 14px;
        }

        .users-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(114, 84, 49, .34);
            border-radius: 14px;
            padding: 18px 18px 16px;
            background:
                radial-gradient(520px 180px at 8% -10%, rgba(255, 239, 203, .72), transparent 62%),
                linear-gradient(180deg, rgba(248, 237, 215, .96), rgba(233, 214, 180, .94));
            box-shadow: 0 14px 30px rgba(67, 45, 20, .14);
        }

        .users-hero::after {
            content: "";
            position: absolute;
            right: -38px;
            top: -46px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(194, 147, 86, .2), rgba(194, 147, 86, 0));
            pointer-events: none;
        }

        .users-hero-head {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            flex-wrap: wrap;
        }

        .users-hero-title {
            margin: 0;
            color: #4a3117;
            font-family: "Cinzel", "Times New Roman", serif;
            letter-spacing: .04em;
            font-size: clamp(1.2rem, 2vw, 1.7rem);
        }

        .users-hero-text {
            margin: 6px 0 0;
            color: #6d5234;
            max-width: 780px;
            line-height: 1.45;
        }

        .users-badge {
            flex: 0 0 auto;
            border: 1px solid rgba(126, 90, 46, .34);
            border-radius: 999px;
            padding: 7px 12px;
            background: rgba(255, 248, 233, .78);
            color: #624424;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            box-shadow: 0 6px 14px rgba(84, 56, 25, .08);
        }

        .users-search-panel {
            padding: 16px;
            border-radius: 14px;
        }

        .users-search-form {
            display: grid;
            gap: 12px;
            grid-template-columns: minmax(0, 1fr) auto auto;
            align-items: end;
        }

        .users-search-input {
            margin: 0;
        }

        .users-search-input input {
            height: 48px;
            font-size: 1rem;
            background: rgba(255,255,255,.72);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.44);
        }

        .users-table-panel {
            padding: 0;
            overflow: hidden;
            border-radius: 14px;
        }

        .users-panel-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 14px 16px 12px;
            border-bottom: 1px solid rgba(114, 84, 49, .18);
            background: linear-gradient(180deg, rgba(255,255,255,.24), rgba(255,255,255,.1));
        }

        .users-panel-title {
            margin: 0;
            color: #56391b;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1rem;
            letter-spacing: .04em;
        }

        .users-panel-meta {
            color: #75583a;
            font-size: .86rem;
        }

        .users-table-wrap {
            overflow-x: auto;
            padding: 0 16px 10px;
        }

        .users-table {
            width: 100%;
            min-width: 980px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .users-table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: rgba(241, 228, 201, .96);
            box-shadow: inset 0 -1px 0 rgba(114, 84, 49, .16);
        }

        .users-table td,
        .users-table th {
            padding: 14px 12px;
        }

        .users-table tbody tr:hover {
            background: rgba(255,255,255,.16);
        }

        .users-table td {
            vertical-align: top;
        }

        .users-name {
            color: #342415;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .users-email {
            color: #584028;
            word-break: break-word;
        }

        .users-date {
            color: #503823;
            white-space: nowrap;
        }

        .role-form {
            display: grid;
            gap: 10px;
            justify-items: start;
            min-width: 180px;
        }

        .role-form select {
            width: 100%;
            min-width: 180px;
            height: 42px;
            border: 1px solid rgba(101,74,42,.28);
            border-radius: 10px;
            background: rgba(255,255,255,.82);
            color: #2d2318;
            padding: 0 12px;
            font-family: inherit;
        }

        .role-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 5px 10px;
            font-size: .78rem;
            letter-spacing: .05em;
            text-transform: uppercase;
            border: 1px solid rgba(95, 69, 37, .2);
            background: rgba(255,255,255,.54);
            color: #6e512f;
        }

        .users-actions {
            display: grid;
            gap: 8px;
            justify-items: start;
            min-width: 220px;
        }

        .users-password-form {
            display: grid;
            gap: 8px;
            width: min(280px, 100%);
        }

        .users-password-form input {
            width: 100%;
        }

        .users-status {
            display: grid;
            gap: 6px;
            min-width: 190px;
        }

        .users-self-note {
            max-width: 220px;
            line-height: 1.35;
        }

        .users-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 16px 16px;
            border-top: 1px solid rgba(114, 84, 49, .16);
            flex-wrap: wrap;
        }

        .users-pagination-pages {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .empty-users {
            padding: 18px 16px 20px;
        }

        @media (max-width: 900px) {
            .users-search-form {
                grid-template-columns: 1fr;
            }

            .users-search-form .btn {
                width: 100%;
                text-align: center;
            }

            .users-table-wrap {
                display: none;
            }

            .users-mobile-list {
                display: grid;
                gap: 12px;
                padding: 14px 16px 8px;
            }

            .users-mobile-card {
                border: 1px solid rgba(101,74,42,.24);
                border-radius: 12px;
                background: rgba(255,255,255,.42);
                padding: 14px;
                display: grid;
                gap: 10px;
                box-shadow: 0 8px 18px rgba(70, 45, 19, .08);
            }

            .users-mobile-meta {
                display: grid;
                gap: 4px;
                color: #5f4730;
            }
        }

        @media (min-width: 901px) {
            .users-mobile-list {
                display: none;
            }
        }
    </style>

    <div class="users-admin-shell">
        <section class="users-hero">
            <div class="users-hero-head">
                <div>
                    <h2 class="users-hero-title">Administration des comptes</h2>
                    <p class="users-hero-text">
                        Consulte les utilisateurs inscrits, ajuste leur rôle et garde une vue claire sur les acces
                        a l'application. Si un compte est bloqué apres 5 échecs, l'utilisateur doit t'ecrire par mail
                        pour demander un nouveau mot de passe.
                    </p>
                </div>
                <div class="users-badge">{{ $users->total() }} compte(s)</div>
            </div>
        </section>

        <section class="panel users-search-panel">
            <form method="GET" action="{{ route('manage.users.index') }}" class="users-search-form">
                <div class="field users-search-input">
                    <label>Recherche</label>
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Nom, e-mail, role...">
                </div>
                <button class="btn" type="submit">Rechercher</button>
                @if(!empty($q))
                    <a class="btn secondary" href="{{ route('manage.users.index') }}">Effacer</a>
                @endif
            </form>
        </section>

        <section class="panel users-table-panel">
            <div class="users-panel-top">
                <h3 class="users-panel-title">Liste des utilisateurs</h3>
                <div class="users-panel-meta">Tri par statut puis par nom</div>
            </div>

            @if($users->isEmpty())
                <div class="empty-users">
                    <p class="muted" style="margin:0;">Aucun compte trouvé.</p>
                </div>
            @else
                <div class="users-table-wrap">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>E-mail</th>
                                <th>Statut</th>
                                <th>Role</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $item)
                                <tr>
                                    <td>
                                        <div class="users-name">{{ $item->name }}</div>
                                    </td>
                                    <td>
                                        <div class="users-email">{{ $item->email }}</div>
                                    </td>
                                    <td>
                                        <div class="users-status">
                                            @if($item->locked_at)
                                                <span class="role-chip">Compte bloqué</span>
                                                <div class="muted">
                                                    5 échecs ou plus. Demande par mail requise avant reinitialisation.
                                                </div>
                                            @elseif($item->password_reset_pending_at)
                                                <span class="role-chip">En attente de reconnexion</span>
                                                <div class="muted">
                                                    Nouveau mot de passe défini. Le compte redevient actif apres une connexion réussie.
                                                </div>
                                            @else
                                                <span class="role-chip">Actif</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('manage.users.update', $item) }}" class="role-form">
                                            @csrf
                                            @method('PUT')
                                            <span class="role-chip">Rôle actuel : {{ ucfirst($item->role) }}</span>
                                            <select name="role">
                                                @foreach($roleOptions as $roleOption)
                                                    <option value="{{ $roleOption }}" {{ $item->role === $roleOption ? 'selected' : '' }}>
                                                        {{ ucfirst($roleOption) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button class="btn secondary" type="submit">Mettre à jour</button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="users-date">{{ optional($item->created_at)->format('d/m/Y H:i') ?: '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="users-actions">
                                            <form method="POST" action="{{ route('manage.users.destroy', $item) }}" class="inline" onsubmit="return confirm('Supprimer ce compte utilisateur ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn danger" type="submit" {{ auth()->id() === $item->id ? 'disabled' : '' }}>Supprimer</button>
                                            </form>
                                            @if($item->locked_at || $item->password_reset_pending_at)
                                                <form method="POST" action="{{ route('manage.users.password', $item) }}" class="users-password-form">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="password" name="password" placeholder="Nouveau mot de passe" minlength="8" required>
                                                    <input type="password" name="password_confirmation" placeholder="Confirmation" minlength="8" required>
                                                    <button class="btn secondary" type="submit">Définir un nouveau mot de passe</button>
                                                </form>
                                            @endif
                                            @if(auth()->id() === $item->id)
                                                <div class="muted users-self-note">Ton compte ne peut pas etre supprime ici.</div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="users-mobile-list">
                    @foreach($users as $item)
                        <article class="users-mobile-card">
                            <div class="users-name">{{ $item->name }}</div>
                            <div class="users-mobile-meta">
                                <div><strong>E-mail :</strong> {{ $item->email }}</div>
                                <div><strong>Créé le :</strong> {{ optional($item->created_at)->format('d/m/Y H:i') ?: '-' }}</div>
                                <div>
                                    <strong>Statut :</strong>
                                    {{ $item->locked_at ? 'Compte bloqué' : ($item->password_reset_pending_at ? 'En attente de reconnexion' : 'Actif') }}
                                </div>
                            </div>
                            <form method="POST" action="{{ route('manage.users.update', $item) }}" class="role-form">
                                @csrf
                                @method('PUT')
                                <span class="role-chip">Rôle actuel : {{ ucfirst($item->role) }}</span>
                                <select name="role">
                                    @foreach($roleOptions as $roleOption)
                                        <option value="{{ $roleOption }}" {{ $item->role === $roleOption ? 'selected' : '' }}>
                                            {{ ucfirst($roleOption) }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn secondary" type="submit">Mettre à jour</button>
                            </form>
                            <div class="users-actions">
                                <form method="POST" action="{{ route('manage.users.destroy', $item) }}" class="inline" onsubmit="return confirm('Supprimer ce compte utilisateur ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn danger" type="submit" {{ auth()->id() === $item->id ? 'disabled' : '' }}>Supprimer</button>
                                </form>
                                @if($item->locked_at || $item->password_reset_pending_at)
                                    <div class="muted">L'utilisateur doit te contacter par mail avant réinitialisation.</div>
                                    <form method="POST" action="{{ route('manage.users.password', $item) }}" class="users-password-form">
                                        @csrf
                                        @method('PUT')
                                        <input type="password" name="password" placeholder="Nouveau mot de passe" minlength="8" required>
                                        <input type="password" name="password_confirmation" placeholder="Confirmation" minlength="8" required>
                                        <button class="btn secondary" type="submit">Définir un nouveau mot de passe</button>
                                    </form>
                                @endif
                                @if(auth()->id() === $item->id)
                                    <div class="muted users-self-note">Ton compte ne peut pas etre supprime ici.</div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            <div class="users-pagination">
                <p class="muted" style="margin:0;">
                    Page {{ $users->currentPage() }} / {{ max(1, $users->lastPage()) }}
                </p>
                <div class="users-pagination-pages">
                    @if($users->onFirstPage())
                        <span class="btn secondary" style="opacity:.55; pointer-events:none;">Precedent</span>
                    @else
                        <a class="btn secondary" href="{{ $users->previousPageUrl() }}">Precedent</a>
                    @endif

                    @for($page = 1; $page <= $users->lastPage(); $page++)
                        @if($page === $users->currentPage())
                            <span class="btn" style="pointer-events:none;">{{ $page }}</span>
                        @else
                            <a class="btn secondary" href="{{ $users->url($page) }}">{{ $page }}</a>
                        @endif
                    @endfor

                    @if($users->hasMorePages())
                        <a class="btn secondary" href="{{ $users->nextPageUrl() }}">Suivant</a>
                    @else
                        <span class="btn secondary" style="opacity:.55; pointer-events:none;">Suivant</span>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
