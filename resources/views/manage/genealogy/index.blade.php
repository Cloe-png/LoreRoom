@extends('manage.layout')

@section('title', 'Gestion - Arbre genealogique')
@section('header', 'Arbre genealogique')

@section('content')
    <section class="panel" style="margin-top:0;">
        <form method="GET" action="{{ route('manage.genealogy.index') }}" class="grid-4">
            <div class="field">
                <label>Pivot</label>
                <select name="pivot_mode">
                    <option value="character" {{ ($pivotMode ?? 'character') === 'character' ? 'selected' : '' }}>Personnage</option>
                    <option value="family" {{ ($pivotMode ?? 'character') === 'family' ? 'selected' : '' }}>Famille</option>
                    <option value="both" {{ ($pivotMode ?? 'character') === 'both' ? 'selected' : '' }}>Les deux</option>
                </select>
            </div>
            <div class="field">
                <label>Personnage pivot</label>
                <select name="character_id">
                    @foreach($characters as $character)
                        <option value="{{ $character->id }}" {{ $selectedId == $character->id ? 'selected' : '' }}>{{ $character->display_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Famille pivot</label>
                <select name="family">
                    <option value="">Selectionner</option>
                    @foreach($families as $family)
                        <option value="{{ $family }}" {{ ($selectedFamily ?? '') === $family ? 'selected' : '' }}>{{ $family }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field" style="align-self:end;">
                <button class="btn" type="submit">Afficher l'arbre</button>
            </div>
        </form>
    </section>

    @if($nodes->isEmpty())
        <section class="panel">
            <p class="muted">Aucune donnee genealogique disponible.</p>
        </section>
    @else
        <section class="panel">
            <p class="muted" style="margin-top:0;">
                Legende: traits pleins = filiation, pointilles = fratrie (frere/soeur, demi-frere/soeur, jumeaux).
            </p>
            <div style="max-height:700px; overflow:auto; border:1px dashed rgba(114,84,49,.35); border-radius:10px; padding:10px; background:rgba(255,255,255,.2);">
                <svg width="{{ $layout['width'] }}" height="{{ $layout['height'] }}" viewBox="0 0 {{ $layout['width'] }} {{ $layout['height'] }}" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Arbre genealogique">
                    <defs>
                        <marker id="tree-arrow" markerWidth="10" markerHeight="8" refX="9" refY="4" orient="auto">
                            <polygon points="0,0 10,4 0,8" fill="rgba(76,95,124,.95)" />
                        </marker>
                    </defs>

                    @foreach($edges as $edge)
                        @php
                            $p1 = $layout['positions'][$edge['from']] ?? null;
                            $p2 = $layout['positions'][$edge['to']] ?? null;
                            $isSibling = ($edge['kind'] ?? '') === 'sibling';
                        @endphp
                        @if($p1 && $p2)
                            @if($isSibling)
                                <line
                                    x1="{{ $p1['x'] }}"
                                    y1="{{ $p1['y'] }}"
                                    x2="{{ $p2['x'] }}"
                                    y2="{{ $p2['y'] }}"
                                    stroke="rgba(103,86,58,.75)"
                                    stroke-width="2.2"
                                    stroke-dasharray="5 4"
                                />
                                <text x="{{ ($p1['x'] + $p2['x']) / 2 }}" y="{{ (($p1['y'] + $p2['y']) / 2) - 7 }}" text-anchor="middle" fill="#5a4223" font-size="10" font-family="Georgia, serif">{{ $edge['label'] }}</text>
                            @else
                                @php
                                    $x1 = $p1['x'];
                                    $y1 = $p1['y'] + 38;
                                    $x2 = $p2['x'];
                                    $y2 = $p2['y'] - 38;
                                @endphp
                                <path d="M {{ $x1 }} {{ $y1 }} C {{ $x1 }} {{ $y1 + 38 }}, {{ $x2 }} {{ $y2 - 38 }}, {{ $x2 }} {{ $y2 }}" stroke="rgba(76,95,124,.92)" stroke-width="2.8" fill="none" marker-end="url(#tree-arrow)" />
                                <text x="{{ ($x1 + $x2) / 2 }}" y="{{ (($y1 + $y2) / 2) - 6 }}" text-anchor="middle" fill="#5a4223" font-size="10.5" font-family="Georgia, serif">{{ $edge['label'] }}</text>
                            @endif
                        @endif
                    @endforeach

                    @foreach($nodes as $node)
                        @php
                            $pos = $layout['positions'][$node['id']] ?? null;
                            if (!$pos) continue;
                            $isDead = ($node['status'] ?? '') === 'mort';
                            $isSelected = (int) $node['id'] === (int) $selectedId;
                            $dates = trim((string) (($node['birth_date'] ?? '?') . ' - ' . ($node['death_date'] ?? '...')));
                        @endphp

                        <circle cx="{{ $pos['x'] }}" cy="{{ $pos['y'] }}" r="33" fill="{{ $isDead ? '#8f96a6' : '#dcc39d' }}" stroke="{{ $isSelected ? '#7d5327' : ($isDead ? '#4a5160' : '#936733') }}" stroke-width="{{ $isSelected ? '4.8' : '3.8' }}" />

                        @if(!empty($node['image_path']))
                            <clipPath id="genealogy-photo-{{ $node['id'] }}">
                                <circle cx="{{ $pos['x'] }}" cy="{{ $pos['y'] }}" r="23" />
                            </clipPath>
                            <image
                                href="{{ route('media.show', ['path' => $node['image_path']]) }}"
                                x="{{ $pos['x'] - 23 }}"
                                y="{{ $pos['y'] - 23 }}"
                                width="46"
                                height="46"
                                preserveAspectRatio="xMidYMid slice"
                                clip-path="url(#genealogy-photo-{{ $node['id'] }})"
                            />
                            <circle cx="{{ $pos['x'] }}" cy="{{ $pos['y'] }}" r="23" fill="none" stroke="{{ $isDead ? '#697181' : '#b1854f' }}" stroke-width="2" />
                        @else
                            <circle cx="{{ $pos['x'] }}" cy="{{ $pos['y'] }}" r="24" fill="{{ $isDead ? '#b9c1cf' : '#efe1c7' }}" stroke="{{ $isDead ? '#697181' : '#b1854f' }}" stroke-width="2" />
                        @endif

                        <text x="{{ $pos['x'] }}" y="{{ $pos['y'] - 44 }}" text-anchor="middle" fill="#4d3a22" font-size="10.5" font-family="Georgia, serif">{{ $node['generation'] }}</text>
                        <text x="{{ $pos['x'] }}" y="{{ $pos['y'] + 53 }}" text-anchor="middle" fill="{{ $isDead ? '#535d6d' : '#3b2b18' }}" font-size="11" font-family="Georgia, serif">{{ \Illuminate\Support\Str::limit($node['name'], 24, '...') }}</text>
                        <text x="{{ $pos['x'] }}" y="{{ $pos['y'] + 67 }}" text-anchor="middle" fill="{{ $isDead ? '#667083' : '#5a4223' }}" font-size="9.5" font-family="Georgia, serif">{{ $dates }}</text>
                    @endforeach
                </svg>
            </div>
        </section>
    @endif
@endsection
