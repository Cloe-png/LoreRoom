@extends('manage.layout')

@section('title', 'Gestion - Chroniques')
@section('header', 'Chroniques')

@section('content')
    <div class="stack" style="justify-content: space-between;">
        <p class="muted">Entrees narratives et timeline de l univers.</p>
        <a class="btn" href="{{ route('manage.chronicles.create') }}">Nouvelle chronique</a>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Monde</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($chronicles as $chronicle)
                <tr>
                    <td>{{ $chronicle->title }}</td>
                    <td>{{ optional($chronicle->world)->name }}</td>
                    <td>{{ optional($chronicle->event_date)->format('Y-m-d') }}</td>
                    <td>{{ $chronicle->status }}</td>
                    <td class="stack">
                        <a class="btn secondary" href="{{ route('manage.chronicles.show', $chronicle) }}">Voir</a>
                        <a class="btn secondary" href="{{ route('manage.chronicles.edit', $chronicle) }}">Éditer</a>
                        <form class="inline" method="POST" action="{{ route('manage.chronicles.destroy', $chronicle) }}">
                            @csrf @method('DELETE')
                            <button class="btn danger" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Aucune chronique.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:10px;">{{ $chronicles->links() }}</div>
    </section>
@endsection
