@extends('manage.layout')
@section('title', 'Gestion - '.$place->name)
@section('header', $place->name)
@section('content')
<style>
.place-detail{color:var(--ink,#2b1d14);font-family:'Cormorant Garamond',serif}
.place-detail h1,.place-detail h2,.place-detail h3{font-family:Cinzel,serif}
.place-detail .paper-card{background:rgba(255,250,236,.55);border:1px solid #9a7f48;outline:1px solid #c7ac72;outline-offset:-5px;border-radius:3px;box-shadow:3px 4px 0 rgba(70,45,20,.22);padding:20px;margin-bottom:18px}
.place-detail .columns{display:grid;grid-template-columns:minmax(0,2fr) minmax(240px,1fr);gap:20px}
.place-detail .btn{font:600 11px Cinzel,serif;letter-spacing:.16em;text-transform:uppercase;border:1px solid #6b1e2a;border-radius:2px;background:#6b1e2a;color:#f6e7c4;padding:9px 15px;box-shadow:2px 3px 0 rgba(40,15,10,.35)}
.place-detail .stamp{display:inline-block;border:2px solid #6b1e2a;outline:1px solid #a8842f;outline-offset:2px;color:#6b1e2a;padding:5px 8px;transform:rotate(-5deg);font:11px Cinzel,serif}
.place-detail .secondary{background:transparent;color:#6b1e2a;box-shadow:none}
.place-detail .private{border-color:#6b1e2a;background:rgba(107,30,42,.06)}
.place-detail img{max-width:100%;max-height:360px;object-fit:cover}
.place-detail a:focus-visible,.place-detail button:focus-visible{outline:2px solid #6b1e2a;outline-offset:3px}
@media(max-width:820px){.place-detail .columns{grid-template-columns:1fr}}
</style>
<main class="place-detail">
    <nav aria-label="Fil d’Ariane" class="muted">
        <a href="{{ route('manage.places.index') }}">Lieux</a>
        @foreach($ancestors as $ancestor)
            › <a href="{{ route('manage.places.show', $ancestor) }}">{{ $ancestor->name }}</a>
        @endforeach
        › {{ $place->name }}
    </nav>

    <header class="paper-card">
        <div class="place-meta">{{ ucfirst($place->type) }} · <span class="stamp">{{ ucfirst(str_replace('_', ' ', $place->status)) }}</span> · {{ ucfirst($place->visibility) }}</div>
        <h1>{{ $place->name }}</h1>
        <p>{{ $place->short_description ?: $place->summary }}</p>
        @if($place->image_path)
            <img src="{{ route('media.show', ['path' => $place->image_path]) }}" alt="Illustration de {{ $place->name }}">
        @endif
    </header>

    <div class="columns">
        <div>
            @if($place->description || $place->atmosphere || $place->climate || $place->population !== null || $place->alt_names)
                <section class="paper-card">
                    <h2>Identité et ambiance</h2>
                    @if($place->description)<p>{{ $place->description }}</p>@endif
                    @if($place->atmosphere)<p><strong>Ambiance :</strong> {{ implode(', ', $place->atmosphere) }}</p>@endif
                    @if($place->climate)<p><strong>Climat :</strong> {{ $place->climate }}</p>@endif
                    @if($place->population !== null)<p><strong>Population :</strong> {{ number_format($place->population, 0, ',', ' ') }}</p>@endif
                    @if($place->alt_names)<p><strong>Autres noms :</strong> {{ implode(', ', $place->alt_names) }}</p>@endif
                </section>
            @endif

            @if($place->government || $place->religion || $place->economy || $place->dangers || $place->languages)
                <section class="paper-card">
                    <h2>Société et pouvoir</h2>
                    @foreach(['government'=>'Gouvernement', 'religion'=>'Religion', 'languages'=>'Langues', 'economy'=>'Économie', 'dangers'=>'Dangers'] as $field => $label)
                        @if($place->$field)
                            <p><strong>{{ $label }} :</strong> {{ is_array($place->$field) ? implode(', ', $place->$field) : $place->$field }}</p>
                        @endif
                    @endforeach
                </section>
            @endif

            @if($place->founded_at || $place->history || $place->chronicles->isNotEmpty() || $place->chapters->isNotEmpty())
                <section class="paper-card">
                    <h2>Histoire</h2>
                    @if($place->founded_at)<p><strong>Fondé :</strong> {{ $place->founded_at }}</p>@endif
                    @if($place->history)<p>{{ $place->history }}</p>@endif
                    @if($place->chronicles->isNotEmpty())
                        <h3>Événements</h3>
                        @foreach($place->chronicles as $item)<p><a href="{{ route('manage.chronicles.show', $item) }}">{{ $item->title }}</a></p>@endforeach
                    @endif
                    @if($place->chapters->isNotEmpty())
                        <h3>Chapitres</h3>
                        @foreach($place->chapters as $item)<p><a href="{{ route('manage.chapters.show', $item) }}">{{ $item->title }}</a></p>@endforeach
                    @endif
                </section>
            @endif

            @if($place->pointsOfInterest->isNotEmpty())
                <section class="paper-card">
                    <h2>Points d’intérêt</h2>
                    @foreach($place->pointsOfInterest as $point)
                        <h3>{{ $point->name }}</h3>
                        @if($point->description)<p>{{ $point->description }}</p>@endif
                    @endforeach
                </section>
            @endif

            @if($place->galleryImages->isNotEmpty())
                <section class="paper-card">
                    <h2>Galerie</h2>
                    @foreach($place->galleryImages as $image)
                        <img src="{{ route('media.show', ['path' => $image->image_path]) }}" alt="{{ $image->caption ?: 'Image de '.$place->name }}">
                    @endforeach
                </section>
            @endif

            @if($place->private_notes)
                <section class="paper-card private"><h2>🔒 Notes privées</h2><p>{{ $place->private_notes }}</p></section>
            @endif
        </div>

        <aside>
            <section class="paper-card">
                <h2>Lié à…</h2>
                @if($place->children->isNotEmpty())
                    <h3>Lieux enfants</h3>
                    @foreach($place->children as $item)<p><a href="{{ route('manage.places.show', $item) }}">{{ $item->name }}</a></p>@endforeach
                @endif
                @if($place->connectedPlaces->isNotEmpty())
                    <h3>Lieux connectés</h3>
                    @foreach($place->connectedPlaces as $item)<p><a href="{{ route('manage.places.show', $item) }}">{{ $item->name }}</a> <small>{{ $item->pivot->travel_note }}</small></p>@endforeach
                @endif
                @foreach(['leaders'=>'Dirigeants', 'factions'=>'Factions', 'species'=>'Espèces', 'characters'=>'Personnages', 'eventChronicles'=>'Chroniques', 'chronicles'=>'Événements'] as $relation => $label)
                    @if($place->$relation->isNotEmpty())
                        <h3>{{ $label }}</h3>
                        @foreach($place->$relation as $item)
                            @php($routeName = in_array($relation, ['eventChronicles', 'chronicles'], true) ? 'manage.chronicles.show' : 'manage.'.(($relation === 'leaders' || $relation === 'characters') ? 'characters' : $relation).'.show')
                            <p><a href="{{ route($routeName, $item) }}">{{ $item->display_name ?? $item->name ?? $item->title }}</a></p>
                        @endforeach
                    @endif
                @endforeach
            </section>
        </aside>
    </div>

    <div class="stack">
        <a class="btn" href="{{ route('manage.places.edit', $place) }}">Modifier</a>
        <form method="POST" action="{{ route('manage.places.duplicate', $place) }}">@csrf<button class="btn secondary" type="submit">Dupliquer</button></form>
        <form method="POST" action="{{ route('manage.places.destroy', $place) }}" onsubmit="return confirm('Supprimer ce lieu ? Ses enfants seront rattachés au parent de ce lieu.')">@csrf @method('DELETE')<button class="btn secondary" type="submit">Supprimer</button></form>
        <a class="btn secondary" href="{{ route('manage.places.index') }}">Retour</a>
    </div>
</main>
@endsection
