@extends('manage.layout')

@section('title', 'Gestion - Mondes')
@section('header', 'Mondes')

@section('content')
    <div class="stack" style="justify-content: space-between;">
        <p class="muted">Structure principale de chaque univers.</p>
        <a class="btn" href="{{ route('manage.worlds.create') }}">Nouveau monde</a>
    </div>

    <section class="panel">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Carte</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($worlds as $world)
                <tr>
                    <td>{{ $world->name }}</td>
                    <td>{{ ucfirst($world->geography_type ?? 'pays') }}</td>
                    <td>
                        @if($world->map_path)
                            <a class="btn secondary" href="{{ asset('storage/'.$world->map_path) }}" target="_blank" rel="noopener">Voir carte</a>
                        @else
                            <span class="muted">Aucune</span>
                        @endif
                    </td>
                    <td class="stack">
                        <a class="btn secondary" href="{{ route('manage.worlds.show', $world) }}">Voir</a>
                        <a class="btn secondary" href="{{ route('manage.worlds.edit', $world) }}">Éditer</a>
                        <form class="inline" method="POST" action="{{ route('manage.worlds.destroy', $world) }}">
                            @csrf @method('DELETE')
                            <button class="btn danger" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Aucun monde.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:10px;">{{ $worlds->links() }}</div>
    </section>
@endsection
