<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche personnage</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e1e1e; font-size: 12px; }
        h1, h2, h3 { margin: 0 0 8px; }
        .muted { color: #666; }
        .section { margin-top: 14px; padding-top: 10px; border-top: 1px solid #ccc; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { vertical-align: top; padding: 6px; }
        .label { font-weight: bold; }
        .card { border: 1px solid #ddd; padding: 8px; border-radius: 6px; }
        .portrait { width: 180px; max-height: 240px; object-fit: contain; border: 1px solid #ddd; }
        table.tbl { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.tbl th, table.tbl td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        table.tbl th { background: #f5f5f5; }
        .gallery-img { width: 160px; height: 130px; object-fit: contain; border: 1px solid #ddd; margin-right: 8px; margin-bottom: 8px; }
    </style>
</head>
<body>
@php
    $children = $character->childrenFromFather->merge($character->childrenFromMother)->unique('id');
@endphp

<h1>{{ $character->display_name }}</h1>
<p class="muted">Fiche personnage exportée en PDF</p>

<table class="grid">
    <tr>
        <td style="width: 200px;">
            @if($portraitDataUri)
                <img class="portrait" src="{{ $portraitDataUri }}" alt="Portrait">
            @else
                <div class="card muted">Aucune photo</div>
            @endif
        </td>
        <td>
            <div class="card">
                <p><span class="label">Nom:</span> {{ $character->display_name }}</p>
                <p><span class="label">Alias:</span> {{ $character->aliases ?: '-' }}</p>
                <p><span class="label">Statut:</span> {{ $character->status ?: '-' }}</p>
                <p><span class="label">Age:</span> {{ $character->calculated_age !== null ? $character->calculated_age . ' ans' : '-' }}</p>
                <p><span class="label">Monde:</span> {{ optional($character->world)->name ?: '-' }}</p>
                <p><span class="label">Rôle:</span> {{ $character->role ?: '-' }}</p>
                <p><span class="label">Naissance:</span> {{ optional($character->birth_date)->format('Y-m-d') ?: '-' }}</p>
                <p><span class="label">Mort:</span> {{ optional($character->death_date)->format('Y-m-d') ?: '-' }}</p>
                <p><span class="label">Lieu de naissance:</span> {{ optional($character->birthPlace)->name ?: '-' }}</p>
                <p><span class="label">Résidence:</span> {{ optional($character->residencePlace)->name ?: '-' }}</p>
                <p><span class="label">Parents:</span> {{ optional($character->father)->display_name ?: 'Inconnu' }} / {{ optional($character->mother)->display_name ?: 'Inconnue' }}</p>
                <p><span class="label">Enfants:</span> {{ $children->isEmpty() ? 'Aucun' : $children->pluck('display_name')->join(', ') }}</p>
            </div>
        </td>
    </tr>
</table>

<div class="section">
    <h2>Pouvoirs et objectifs</h2>
    <p><span class="label">Pouvoir actif:</span> {{ $character->has_power ? 'Oui' : 'Non' }}</p>
    <p><span class="label">Niveau:</span> {{ $character->power_level ?: '-' }}/10</p>
    <p><span class="label">Description:</span> {{ $character->power_description ?: '-' }}</p>
    <p><span class="label">Objectif court terme:</span> {{ $character->short_term_goal ?: '-' }}</p>
    <p><span class="label">Objectif long terme:</span> {{ $character->long_term_goal ?: '-' }}</p>
    <p><span class="label">Secrets:</span>
        @if($character->secrets_is_private && trim((string)$character->secrets) !== '')
            (masqués)
        @else
            {{ $character->secrets ?: '-' }}
        @endif
    </p>
</div>

<div class="section">
    <h2>Apparence et psychologie</h2>
    <p><span class="label">Cheveux:</span> {{ $character->hair_color ?: $character->hair_eyes ?: '-' }}</p>
    <p><span class="label">Yeux:</span> {{ $character->eye_color ?: $character->hair_eyes ?: '-' }}</p>
    <p><span class="label">Marques:</span> {{ $character->marks ?: '-' }}</p>
    <p><span class="label">Style:</span> {{ $character->clothing_style ?: '-' }}</p>
    <p><span class="label">Qualités:</span> {{ $character->qualities ?: '-' }}</p>
    <p><span class="label">Défauts:</span> {{ $character->flaws ?: '-' }}</p>
    <p><span class="label">Voix / tics:</span> {{ $character->voice_tics ?: '-' }}</p>
    <p><span class="label">Résumé:</span> {{ $character->summary ?: '-' }}</p>
</div>

<div class="section">
    <h2>Équipements / artefacts</h2>
    @if($character->items->isEmpty())
        <p class="muted">Aucun équipement.</p>
    @else
        <table class="tbl">
            <thead><tr><th>Nom</th><th>Rareté</th><th>Notes</th></tr></thead>
            <tbody>
            @foreach($character->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->rarity ?: '-' }}</td>
                    <td>{{ $item->notes ?: '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="section">
    <h2>Métiers</h2>
    @if($character->jobs->isEmpty())
        <p class="muted">Aucun métier.</p>
    @else
        <table class="tbl">
            <thead><tr><th>Métier</th><th>Début</th><th>Fin</th><th>Notes</th></tr></thead>
            <tbody>
            @foreach($character->jobs as $job)
                <tr>
                    <td>{{ $job->job_name }}</td>
                    <td>{{ $job->start_year ?: '-' }}</td>
                    <td>{{ $job->end_year ?: '-' }}</td>
                    <td>{{ $job->notes ?: '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="section">
    <h2>Timeline perso</h2>
    @if($character->events->isEmpty())
        <p class="muted">Aucun événement.</p>
    @else
        <table class="tbl">
            <thead><tr><th>Date</th><th>Titre</th><th>Détails</th></tr></thead>
            <tbody>
            @foreach($character->events as $event)
                <tr>
                    <td>{{ optional($event->event_date)->format('Y-m-d') ?: '-' }}</td>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->details ?: '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="section">
    <h2>Galerie</h2>
    @if($character->galleryImages->isEmpty())
        <p class="muted">Aucune image.</p>
    @else
        @foreach($character->galleryImages as $img)
            @php $dataUri = $galleryDataUris[$img->id] ?? null; @endphp
            @if($dataUri)
                <div style="display:inline-block; text-align:center;">
                    <img class="gallery-img" src="{{ $dataUri }}" alt="Galerie">
                    <div class="muted">{{ $img->caption ?: 'Sans légende' }}</div>
                </div>
            @endif
        @endforeach
    @endif
</div>

</body>
</html>
