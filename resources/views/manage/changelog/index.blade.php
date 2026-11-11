@extends('manage.layout')
@section('title', 'Nouveautés')
@section('header', 'Nouveautés')
@section('content')
<section class="home-card">
    @if($canManage)<p><a class="home-btn" href="{{ route('manage.nouveautes.create') }}">Ajouter une nouveauté</a></p>@endif
    @forelse($entries as $entry)
        <article><time>{{ \Carbon\Carbon::parse($entry->published_at)->format('d/m/Y') }}</time><h2>{{ $entry->title }}</h2><div>{!! $entry->body !!}</div>
            @if($canManage)<p><a href="{{ route('manage.nouveautes.edit', $entry->id) }}">Modifier</a>
                <form method="POST" action="{{ route('manage.nouveautes.destroy', $entry->id) }}">@csrf @method('DELETE')<button type="submit">Supprimer</button></form></p>@endif
        </article>
    @empty<p class="home-empty">Aucune nouveauté publiée.</p>@endforelse
    {{ $entries->links() }}
</section>
<form method="POST" action="{{ route('manage.nouveautes.seen') }}">@csrf<button class="home-btn home-btn--secondary" type="submit">Marquer comme lues</button></form>
@endsection
