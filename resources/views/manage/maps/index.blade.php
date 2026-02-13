@extends('manage.layout')

@section('title', 'Gestion - Cartes imaginaires')
@section('header', 'Cartes imaginaires')

@section('content')
    <div class="stack" style="justify-content: space-between;">
        <p class="muted">Creation et gestion des cartes de monde, regions et cites.</p>
        <a class="btn" href="{{ route('manage.maps.create') }}">Nouvelle carte</a>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Monde</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($maps as $map)
                <tr>
                    <td>{{ $map->title }}</td>
                    <td>{{ optional($map->world)->name }}</td>
                    <td>{{ $map->map_type ?: '-' }}</td>
                    <td>{{ $map->status }}</td>
                    <td class="stack">
                        <a class="btn secondary" href="{{ route('manage.maps.show', $map) }}">Voir</a>
                        <a class="btn secondary" href="{{ route('manage.maps.edit', $map) }}">Éditer</a>
                        <form class="inline" method="POST" action="{{ route('manage.maps.destroy', $map) }}">
                            @csrf @method('DELETE')
                            <button class="btn danger" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Aucune carte imaginaire.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:10px;">{{ $maps->links() }}</div>
    </section>
@endsection
