@extends('manage.layout')

@section('title', 'Gestion - Lieux')
@section('header', 'Lieux')

@section('content')
    <div class="stack" style="justify-content: space-between;">
        <p class="muted">Atlas des lieux et regions.</p>
        <a class="btn" href="{{ route('manage.places.create') }}">Nouveau lieu</a>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Monde</th>
                    <th>Region</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($places as $place)
                <tr>
                    <td>{{ $place->name }}</td>
                    <td>{{ optional($place->world)->name }}</td>
                    <td>{{ $place->region }}</td>
                    <td class="stack">
                        <a class="btn secondary" href="{{ route('manage.places.show', $place) }}">Voir</a>
                        <a class="btn secondary" href="{{ route('manage.places.edit', $place) }}">Éditer</a>
                        <form class="inline" method="POST" action="{{ route('manage.places.destroy', $place) }}">
                            @csrf @method('DELETE')
                            <button class="btn danger" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Aucun lieu.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:10px;">{{ $places->links() }}</div>
    </section>
@endsection
