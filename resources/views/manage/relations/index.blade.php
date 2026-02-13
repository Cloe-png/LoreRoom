@extends('manage.layout')

@section('title', 'Gestion - Relations')
@section('header', 'Relations personnages')

@section('content')
    <div class="stack" style="justify-content: space-between;">
        <p class="muted">Module relationnel entre personnages (famille, alliance, rivalite, mentorat...).</p>
        <a class="btn" href="{{ route('manage.relations.create') }}">Nouvelle relation</a>
    </div>

    <section class="panel" style="margin-top:8px;">
        <form method="GET" action="{{ route('manage.relations.index') }}" class="stack" style="align-items:flex-end;">
            <div class="field" style="margin:0; min-width:min(420px, 100%);">
                <label>Recherche relation</label>
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Type, description, nom de personnage...">
            </div>
            <button class="btn" type="submit">Rechercher</button>
            @if(!empty($q))
                <a class="btn secondary" href="{{ route('manage.relations.index') }}">Effacer</a>
            @endif
        </form>
    </section>

    <section class="panel">
        <h3 style="margin-top:0;">Vue reseau (apercu)</h3>
        @if($graphRelations->isEmpty())
            <p class="muted">Aucune relation a afficher.</p>
        @else
            <div style="max-height:430px; overflow:auto; border:1px dashed rgba(114,84,49,.35); border-radius:8px; padding:8px; background:rgba(255,255,255,.18);">
                <svg width="1100" height="420" viewBox="0 0 1100 420" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Apercu des relations personnages">
                    @php
                        $nodes = [];
                        foreach ($graphRelations as $edge) {
                            if (!empty($edge['from'])) {
                                $nodes[(int) $edge['from_id']] = [
                                    'name' => $edge['from'],
                                    'is_dead' => (($edge['from_status'] ?? null) === 'mort'),
                                    'photo' => $edge['from_photo'] ?? null,
                                ];
                            }
                            if (!empty($edge['to'])) {
                                $nodes[(int) $edge['to_id']] = [
                                    'name' => $edge['to'],
                                    'is_dead' => (($edge['to_status'] ?? null) === 'mort'),
                                    'photo' => $edge['to_photo'] ?? null,
                                ];
                            }
                        }

                        $nodePos = [];
                        $total = max(count($nodes), 1);
                        $centerX = 550;
                        $centerY = 205;
                        $radius = min(155 + ($total * 5), 180);
                        $index = 0;
                        foreach ($nodes as $id => $nodeData) {
                            $angle = (2 * pi() * $index) / $total;
                            $nodePos[$id] = [
                                'x' => $centerX + cos($angle) * $radius,
                                'y' => $centerY + sin($angle) * $radius,
                                'name' => $nodeData['name'],
                                'is_dead' => (bool) ($nodeData['is_dead'] ?? false),
                                'photo' => $nodeData['photo'] ?? null,
                            ];
                            $index++;
                        }
                    @endphp

                    <defs>
                        <radialGradient id="sphere-core" cx="34%" cy="30%" r="70%">
                            <stop offset="0%" stop-color="#fff7e6" />
                            <stop offset="58%" stop-color="#ead2a9" />
                            <stop offset="100%" stop-color="#c29a61" />
                        </radialGradient>
                        <filter id="sphere-shadow" x="-30%" y="-30%" width="160%" height="160%">
                            <feDropShadow dx="0" dy="2.2" stdDeviation="1.6" flood-color="#3c2814" flood-opacity=".35" />
                        </filter>
                        <marker id="arrow-head" markerWidth="10" markerHeight="8" refX="9" refY="4" orient="auto">
                            <polygon points="0,0 10,4 0,8" fill="rgba(67,93,125,.9)" />
                        </marker>
                    </defs>

                    @foreach($graphRelations as $edge)
                        @php
                            $fromId = (int) ($edge['from_id'] ?? 0);
                            $toId = (int) ($edge['to_id'] ?? 0);
                            $p1 = $nodePos[$fromId] ?? null;
                            $p2 = $nodePos[$toId] ?? null;
                        @endphp
                        @if($p1 && $p2)
                            @php
                                $dx = $p2['x'] - $p1['x'];
                                $dy = $p2['y'] - $p1['y'];
                                $len = max(sqrt(($dx * $dx) + ($dy * $dy)), 1);
                                $ux = $dx / $len;
                                $uy = $dy / $len;
                                $x1 = $p1['x'] + ($ux * 33);
                                $y1 = $p1['y'] + ($uy * 33);
                                $x2 = $p2['x'] - ($ux * 33);
                                $y2 = $p2['y'] - ($uy * 33);
                                $mx = ($x1 + $x2) / 2;
                                $my = ($y1 + $y2) / 2;
                            @endphp
                            <line
                                x1="{{ $x1 }}"
                                y1="{{ $y1 }}"
                                x2="{{ $x2 }}"
                                y2="{{ $y2 }}"
                                stroke="rgba(67,93,125,.78)"
                                stroke-width="2.5"
                                @if(!$edge['bidirectional']) marker-end="url(#arrow-head)" @endif
                            />
                            <rect x="{{ $mx - 26 }}" y="{{ $my - 10 }}" width="52" height="16" rx="7" fill="rgba(244,235,216,.9)" stroke="rgba(111,77,38,.35)" />
                            <text x="{{ $mx }}" y="{{ $my + 2 }}" text-anchor="middle" fill="#4f3b21" font-size="10.5" font-family="Georgia, serif">{{ \Illuminate\Support\Str::limit((string) ($edge['type'] ?? ''), 10, '') }}</text>
                        @endif
                    @endforeach

                    @foreach($nodePos as $id => $p)
                        @php
                            $photoPath = $p['photo'] ?? null;
                        @endphp
                        <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="31" fill="{{ !empty($p['is_dead']) ? '#8f96a6' : 'url(#sphere-core)' }}" stroke="{{ !empty($p['is_dead']) ? '#4b5260' : '#7b5528' }}" stroke-width="4" filter="url(#sphere-shadow)" />

                        @if(!empty($photoPath))
                            <clipPath id="node-photo-{{ $id }}">
                                <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="22" />
                            </clipPath>
                            <image
                                href="{{ route('media.show', ['path' => $photoPath]) }}"
                                x="{{ $p['x'] - 22 }}"
                                y="{{ $p['y'] - 22 }}"
                                width="44"
                                height="44"
                                preserveAspectRatio="xMidYMid slice"
                                clip-path="url(#node-photo-{{ $id }})"
                            />
                            <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="22" fill="none" stroke="{{ !empty($p['is_dead']) ? '#6b7382' : '#a57944' }}" stroke-width="2" />
                        @else
                            <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="22" fill="{{ !empty($p['is_dead']) ? '#bbc2cf' : '#efe1c7' }}" stroke="{{ !empty($p['is_dead']) ? '#6b7382' : '#a57944' }}" stroke-width="2" />
                        @endif

                        <text x="{{ $p['x'] }}" y="{{ $p['y'] + 49 }}" text-anchor="middle" fill="{{ !empty($p['is_dead']) ? '#4e5665' : '#3a2a17' }}" font-size="11" font-family="Georgia, serif">{{ \Illuminate\Support\Str::limit($p['name'], 20, '...') }}</text>
                    @endforeach
                </svg>
            </div>
        @endif
    </section>

    <section class="panel">
        @if(!empty($q))
            <p class="muted" style="margin-top:0;">Resultats pour: <strong>{{ $q }}</strong></p>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Depuis</th>
                    <th>Vers</th>
                    <th>Type</th>
                    <th>Intensite</th>
                    <th>Sens</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($relations as $relation)
                @php
                    $from = $relation->fromCharacter;
                    $to = $relation->toCharacter;
                    $fromPhoto = !empty(optional($from)->image_path) ? $from->image_path : optional(optional($from)->primaryGalleryImage)->image_path;
                    $toPhoto = !empty(optional($to)->image_path) ? $to->image_path : optional(optional($to)->primaryGalleryImage)->image_path;
                @endphp
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            @if(!empty($fromPhoto))
                                <img src="{{ route('media.show', ['path' => $fromPhoto]) }}" alt="Photo source" style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:1px solid rgba(101,74,42,.35);">
                            @endif
                            <span>{{ optional($from)->display_name ?: '-' }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            @if(!empty($toPhoto))
                                <img src="{{ route('media.show', ['path' => $toPhoto]) }}" alt="Photo cible" style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:1px solid rgba(101,74,42,.35);">
                            @endif
                            <span>{{ optional($to)->display_name ?: '-' }}</span>
                        </div>
                    </td>
                    <td>{{ $relation->display_type ?? $relation->relation_type }}</td>
                    <td>{{ $relation->intensity ?: '-' }}/10</td>
                    <td>{{ $relation->is_bidirectional ? 'Bidirectionnelle' : 'Unidirectionnelle' }}</td>
                    <td class="stack">
                        <a class="btn secondary" href="{{ route('manage.relations.show', $relation) }}">Voir</a>
                        <a class="btn secondary" href="{{ route('manage.relations.edit', $relation) }}">Editer</a>
                        <form class="inline" method="POST" action="{{ route('manage.relations.destroy', $relation) }}">
                            @csrf @method('DELETE')
                            <button class="btn danger" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">Aucune relation.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:10px;">{{ $relations->links() }}</div>
    </section>
@endsection
