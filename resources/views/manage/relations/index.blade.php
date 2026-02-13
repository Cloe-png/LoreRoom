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
        <h3 style="margin-top:0;">Vue réseau (aperçu)</h3>
        @if($graphRelations->isEmpty())
            <p class="muted">Aucune relation à afficher.</p>
        @else
            <div style="max-height:430px; overflow:auto; border:1px dashed rgba(114,84,49,.35); border-radius:8px; padding:8px; background:rgba(255,255,255,.18);">
                <svg width="1100" height="400" viewBox="0 0 1100 400" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Aperçu des relations personnages">
                    @php
                        $nodes = [];
                        foreach ($graphRelations as $edge) {
                            if (!empty($edge['from'])) {
                                $nodes[(int) $edge['from_id']] = [
                                    'name' => $edge['from'],
                                    'is_dead' => (($edge['from_status'] ?? null) === 'mort'),
                                ];
                            }
                            if (!empty($edge['to'])) {
                                $nodes[(int) $edge['to_id']] = [
                                    'name' => $edge['to'],
                                    'is_dead' => (($edge['to_status'] ?? null) === 'mort'),
                                ];
                            }
                        }

                        $nodePos = [];
                        $total = max(count($nodes), 1);
                        $centerX = 550;
                        $centerY = 200;
                        $radius = min(155 + ($total * 5), 175);
                        $index = 0;
                        foreach ($nodes as $id => $nodeData) {
                            $angle = (2 * pi() * $index) / $total;
                            $x = $centerX + cos($angle) * $radius;
                            $y = $centerY + sin($angle) * $radius;
                            $nodePos[$id] = [
                                'x' => $x,
                                'y' => $y,
                                'name' => $nodeData['name'],
                                'is_dead' => (bool) ($nodeData['is_dead'] ?? false),
                            ];
                            $index++;
                        }

                        // Spread edges that share the same two characters to avoid overlap.
                        $pairBuckets = [];
                        foreach ($graphRelations as $edgeIndex => $edge) {
                            $fromId = (int) ($edge['from_id'] ?? 0);
                            $toId = (int) ($edge['to_id'] ?? 0);
                            if (!$fromId || !$toId) {
                                continue;
                            }

                            $a = min($fromId, $toId);
                            $b = max($fromId, $toId);
                            $pairKey = $a . '-' . $b;
                            $pairBuckets[$pairKey][] = $edgeIndex;
                        }

                        $edgeMeta = [];
                        foreach ($pairBuckets as $pairIndexes) {
                            $count = count($pairIndexes);
                            foreach ($pairIndexes as $slot => $edgeIndex) {
                                $offset = ($slot - (($count - 1) / 2)) * 24;
                                $edgeMeta[$edgeIndex] = ['offset' => $offset];
                            }
                        }
                    @endphp

                    <defs>
                        <radialGradient id="sphere-core" cx="34%" cy="30%" r="70%">
                            <stop offset="0%" stop-color="#fff7e6" />
                            <stop offset="58%" stop-color="#ead2a9" />
                            <stop offset="100%" stop-color="#c29a61" />
                        </radialGradient>
                        <radialGradient id="sphere-inner" cx="36%" cy="32%" r="68%">
                            <stop offset="0%" stop-color="#fffaf0" />
                            <stop offset="100%" stop-color="#d5b384" />
                        </radialGradient>
                        <filter id="sphere-shadow" x="-30%" y="-30%" width="160%" height="160%">
                            <feDropShadow dx="0" dy="2.2" stdDeviation="1.6" flood-color="#3c2814" flood-opacity=".35" />
                        </filter>
                        <marker id="arrow-head" markerWidth="10" markerHeight="8" refX="9" refY="4" orient="auto">
                            <polygon points="0,0 10,4 0,8" fill="rgba(67,93,125,.9)" />
                        </marker>
                    </defs>

                    @foreach($graphRelations as $edgeIndex => $edge)
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
                                $nx = -$dy / $len;
                                $ny = $dx / $len;
                                $mx = ($p1['x'] + $p2['x']) / 2;
                                $my = ($p1['y'] + $p2['y']) / 2;
                                $offset = $edgeMeta[$edgeIndex]['offset'] ?? 0;
                                $nodeRadius = 31;
                                $x1 = $p1['x'] + ($ux * $nodeRadius);
                                $y1 = $p1['y'] + ($uy * $nodeRadius);
                                $x2 = $p2['x'] - ($ux * $nodeRadius);
                                $y2 = $p2['y'] - ($uy * $nodeRadius);
                                $cx = $mx + ($nx * $offset);
                                $cy = $my + ($ny * $offset);
                                $t = ($fromId <= $toId) ? 0.42 : 0.58;
                                $bx = ((1 - $t) * (1 - $t) * $x1) + (2 * (1 - $t) * $t * $cx) + ($t * $t * $x2);
                                $by = ((1 - $t) * (1 - $t) * $y1) + (2 * (1 - $t) * $t * $cy) + ($t * $t * $y2);
                                $labelX = $bx + ($nx * 14);
                                $labelY = $by + ($ny * 14);
                                $label = (string) ($edge['type'] ?? '');
                                $labelLen = function_exists('mb_strlen') ? mb_strlen($label) : strlen($label);
                                $labelW = max(36, (int) ($labelLen * 6.3) + 12);
                                $labelH = 16;
                            @endphp
                            <path
                                d="M {{ $x1 }} {{ $y1 }} Q {{ $cx }} {{ $cy }}, {{ $x2 }} {{ $y2 }}"
                                stroke="rgba(67,93,125,.78)"
                                stroke-width="2.5"
                                fill="none"
                                @if(!$edge['bidirectional']) marker-end="url(#arrow-head)" @endif
                            />
                            <rect x="{{ $labelX - ($labelW / 2) }}" y="{{ $labelY - 12 }}" width="{{ $labelW }}" height="{{ $labelH }}" rx="7" fill="rgba(244,235,216,.9)" stroke="rgba(111,77,38,.35)" />
                            <text x="{{ $labelX }}" y="{{ $labelY }}" text-anchor="middle" fill="#4f3b21" font-size="11" font-family="Georgia, serif">{{ $edge['type'] }}</text>
                        @endif
                    @endforeach

                    @foreach($nodePos as $id => $p)
                        @if(!empty($p['is_dead']))
                            <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="30" fill="#8f96a6" stroke="#4b5260" stroke-width="4" filter="url(#sphere-shadow)" />
                            <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="22" fill="#bbc2cf" stroke="#6b7382" stroke-width="2" />
                            <line x1="{{ $p['x'] - 11 }}" y1="{{ $p['y'] - 11 }}" x2="{{ $p['x'] + 11 }}" y2="{{ $p['y'] + 11 }}" stroke="#3e4552" stroke-width="2.1" />
                            <line x1="{{ $p['x'] - 11 }}" y1="{{ $p['y'] + 11 }}" x2="{{ $p['x'] + 11 }}" y2="{{ $p['y'] - 11 }}" stroke="#3e4552" stroke-width="2.1" />
                            <text x="{{ $p['x'] }}" y="{{ $p['y'] + 49 }}" text-anchor="middle" fill="#4e5665" font-size="11" font-family="Georgia, serif">✝ {{ Str::limit($p['name'], 18, '...') }}</text>
                        @else
                            <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="30" fill="url(#sphere-core)" stroke="#7b5528" stroke-width="4" filter="url(#sphere-shadow)" />
                            <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="22" fill="url(#sphere-inner)" stroke="#a57944" stroke-width="2" />
                            <ellipse cx="{{ $p['x'] - 8 }}" cy="{{ $p['y'] - 9 }}" rx="7" ry="4" fill="rgba(255,255,255,.42)" />
                            <text x="{{ $p['x'] }}" y="{{ $p['y'] + 49 }}" text-anchor="middle" fill="#3a2a17" font-size="11" font-family="Georgia, serif">{{ Str::limit($p['name'], 20, '...') }}</text>
                        @endif
                    @endforeach
                </svg>
            </div>
        @endif
    </section>

    <section class="panel">
        @if(!empty($q))
            <p class="muted" style="margin-top:0;">Résultats pour: <strong>{{ $q }}</strong></p>
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
                <tr>
                    <td>{{ optional($relation->fromCharacter)->display_name ?: '-' }}</td>
                    <td>{{ optional($relation->toCharacter)->display_name ?: '-' }}</td>
                    <td>{{ $relation->display_type ?? $relation->relation_type }}</td>
                    <td>{{ $relation->intensity ?: '-' }}/10</td>
                    <td>{{ $relation->is_bidirectional ? 'Bidirectionnelle' : 'Unidirectionnelle' }}</td>
                    <td class="stack">
                        <a class="btn secondary" href="{{ route('manage.relations.show', $relation) }}">Voir</a>
                        <a class="btn secondary" href="{{ route('manage.relations.edit', $relation) }}">Éditer</a>
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
