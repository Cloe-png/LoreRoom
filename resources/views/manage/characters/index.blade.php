@extends('manage.layout')

@section('title', 'Gestion - Personnages')
@section('header', 'Personnages')

@section('content')
    <div class="stack" style="justify-content: space-between;">
        <p class="muted">Catalogue des personnages et de leur rôle narratif.</p>
        <a class="btn" href="{{ route('manage.characters.create') }}">Nouveau personnage</a>
    </div>

    <section class="panel" style="margin-top:8px;">
        <form method="GET" action="{{ route('manage.characters.index') }}" class="stack" style="align-items:flex-end;">
            <div class="field" style="margin:0; min-width:min(420px, 100%);">
                <label>Recherche personnage</label>
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Nom, prénom, rôle, monde, qualités...">
            </div>
            <button class="btn" type="submit">Rechercher</button>
            @if(!empty($q))
                <a class="btn secondary" href="{{ route('manage.characters.index') }}">Effacer</a>
            @endif
        </form>
    </section>

    <section class="panel">
        @if(!empty($q))
            <p class="muted" style="margin-top:0;">Résultats pour: <strong>{{ $q }}</strong></p>
        @endif

        <div style="max-height: 460px; overflow-y: auto; padding-right: 6px; border-left: 2px solid rgba(70,52,33,.35); border-right: 2px solid rgba(70,52,33,.35); border-radius: 8px;">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Habite à</th>
                        <th>Rôle</th>
                        <th>Parents</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($characters as $character)
                    <tr>
                        <td>{{ $character->display_name }}</td>
                        <td>{{ optional($character->world)->name }}</td>
                        <td>{{ $character->role }}</td>
                        <td>
                            {{ optional($character->father)->display_name ?: '-' }}
                            /
                            {{ optional($character->mother)->display_name ?: '-' }}
                        </td>
                        <td class="stack">
                            <a class="btn secondary" href="{{ route('manage.characters.show', $character) }}">Voir</a>
                            <a class="btn secondary" href="{{ route('manage.characters.edit', $character) }}">Éditer</a>
                            <form class="inline" method="POST" action="{{ route('manage.characters.destroy', $character) }}">
                                @csrf @method('DELETE')
                                <button class="btn danger" type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted">Aucun personnage.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:10px;">{{ $characters->links() }}</div>
    </section>
@endsection

