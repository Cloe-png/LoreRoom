@extends('manage.layout')

@section('title', 'Gestion - Galerie')
@section('header', 'Galerie')

@section('content')
    <div class="stack" style="justify-content:space-between; margin-bottom:10px;">
        <div class="muted">{{ $totalCount }} image(s) dans la galerie du musée</div>
    </div>

    <div class="panel" style="margin-top:0;">
        <form method="GET" action="{{ route('manage.gallery.index') }}" class="stack" style="align-items:flex-end;">
            <div class="field" style="margin:0; min-width:260px; flex:1;">
                <label>Recherche</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="Nom du personnage, légende...">
            </div>
            <div class="field" style="margin:0; min-width:180px;">
                <label>Source</label>
                <select name="source">
                    <option value="all" @selected($source === 'all')>Tout</option>
                    <option value="portrait" @selected($source === 'portrait')>Portraits</option>
                    <option value="gallery" @selected($source === 'gallery')>Galerie</option>
                </select>
            </div>
            <button class="btn" type="submit">Filtrer</button>
            <a class="btn secondary" href="{{ route('manage.gallery.index') }}">Réinitialiser</a>
        </form>
    </div>

    @if($images->isEmpty())
        <div class="panel">
            <p class="muted">Aucune image trouvée avec ces filtres.</p>
        </div>
    @else
        <div class="gallery-grid">
            @foreach($images as $image)
                <article class="gallery-card">
                    <a class="gallery-media-link" href="{{ route('manage.characters.show', $image['character_id']) }}">
                        <img class="gallery-media" src="{{ route('media.show', ['path' => $image['image_path']]) }}" alt="{{ $image['character_name'] }}">
                    </a>
                    <div class="gallery-meta">
                        <div class="stack" style="justify-content:space-between;">
                            <strong>{{ $image['character_name'] }}</strong>
                            <span class="gallery-tag">{{ $image['source_label'] }}</span>
                        </div>
                        @if(!empty($image['caption']))
                            <p class="muted" style="margin:6px 0 0;">{{ $image['caption'] }}</p>
                        @endif
                    </div>
                    <span class="pin" style="background:{{ $image['preferred_color'] ?: '#8f6a3a' }};"></span>
                </article>
            @endforeach
        </div>
    @endif

    <style>
        .gallery-grid {
            margin-top: 14px;
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        }
        .gallery-card {
            position: relative;
            border: 1px solid rgba(114, 84, 49, .35);
            border-radius: 12px;
            overflow: hidden;
            background: rgba(255,255,255,.24);
            box-shadow: 0 10px 20px rgba(74, 48, 21, .14);
            transition: transform 140ms ease, box-shadow 140ms ease;
        }
        .gallery-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(74, 48, 21, .2);
        }
        .gallery-media-link {
            display: block;
            background: linear-gradient(180deg, rgba(0,0,0,.05), rgba(0,0,0,.18));
        }
        .gallery-media {
            display: block;
            width: 100%;
            height: 220px;
            object-fit: contain;
            background: rgba(22, 18, 12, .14);
        }
        .gallery-meta {
            padding: 10px 10px 12px;
            color: #2d2318;
        }
        .gallery-tag {
            font-size: .74rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #6f4c26;
        }
        .pin {
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            top: 8px;
            right: 8px;
            border: 1px solid rgba(39, 31, 21, .4);
            box-shadow: 0 0 0 2px rgba(255,255,255,.55);
        }
    </style>
@endsection

