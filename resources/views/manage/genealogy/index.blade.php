@extends('manage.layout')

@section('title', 'Gestion - Arbre généalogique')
@section('header', 'Arbre généalogique')

@section('content')
    <section class="panel" style="margin-top:0;">
        <form method="GET" action="{{ route('manage.genealogy.index') }}" class="stack" style="align-items:flex-end;">
            <div class="field" style="margin:0; min-width:320px;">
                <label>Personnage pivot</label>
                <select name="character_id">
                    @foreach($characters as $character)
                        <option value="{{ $character->id }}" {{ $selectedId == $character->id ? 'selected' : '' }}>{{ $character->display_name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn" type="submit">Afficher l'arbre</button>
        </form>
    </section>

    @if($nodes->isEmpty())
        <section class="panel">
            <p class="muted">Aucune donnée généalogique disponible.</p>
        </section>
    @else
        <section class="panel">
            <div style="max-height:640px; overflow:auto; border:1px dashed rgba(114,84,49,.35); border-radius:10px; padding:10px; background:rgba(255,255,255,.2);">
                <svg width="{{ $layout['width'] }}" height="{{ $layout['height'] }}" viewBox="0 0 {{ $layout['width'] }} {{ $layout['height'] }}" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Arbre généalogique">
                    <defs>
                        <marker id="tree-arrow" markerWidth="10" markerHeight="8" refX="9" refY="4" orient="auto">
                            <polygon points="0,0 10,4 0,8" fill="rgba(76,95,124,.95)" />
                        </marker>
                    </defs>

                    @foreach($edges as $edge)
                        @php
                            $p1 = $layout['positions'][$edge['from']] ?? null;
                            $p2 = $layout['positions'][$edge['to']] ?? null;
                        @endphp
                        @if($p1 && $p2)
                            @php
                                $x1 = $p1['x'];
                                $y1 = $p1['y'] + 38;
                                $x2 = $p2['x'];
                                $y2 = $p2['y'] - 38;
                                $mx = ($x1 + $x2) / 2;
                            @endphp
                            <path d="M {{ $x1 }} {{ $y1 }} C {{ $x1 }} {{ $y1 + 38 }}, {{ $x2 }} {{ $y2 - 38 }}, {{ $x2 }} {{ $y2 }}" stroke="rgba(76,95,124,.92)" stroke-width="2.8" fill="none" marker-end="url(#tree-arrow)" />
                            <text x="{{ $mx }}" y="{{ ($y1 + $y2) / 2 - 6 }}" text-anchor="middle" fill="#5a4223" font-size="10.5" font-family="Georgia, serif">{{ $edge['label'] }}</text>
                        @endif
                    @endforeach

                    @foreach($nodes as $node)
                        @php
                            $pos = $layout['positions'][$node['id']] ?? null;
                            if (!$pos) continue;
                            $isDead = ($node['status'] ?? '') === 'mort';
                            $isSelected = (int) $node['id'] === (int) $selectedId;
                        @endphp

                        @if($isDead)
                            <circle cx="{{ $pos['x'] }}" cy="{{ $pos['y'] }}" r="33" fill="#8f96a6" stroke="#4a5160" stroke-width="{{ $isSelected ? '4.6' : '3.8' }}" />
                            <circle cx="{{ $pos['x'] }}" cy="{{ $pos['y'] }}" r="24" fill="#b9c1cf" stroke="#697181" stroke-width="2" />
                            <line x1="{{ $pos['x'] - 10 }}" y1="{{ $pos['y'] - 10 }}" x2="{{ $pos['x'] + 10 }}" y2="{{ $pos['y'] + 10 }}" stroke="#3c4452" stroke-width="2" />
                            <line x1="{{ $pos['x'] - 10 }}" y1="{{ $pos['y'] + 10 }}" x2="{{ $pos['x'] + 10 }}" y2="{{ $pos['y'] - 10 }}" stroke="#3c4452" stroke-width="2" />
                        @else
                            <circle cx="{{ $pos['x'] }}" cy="{{ $pos['y'] }}" r="33" fill="#dcc39d" stroke="{{ $isSelected ? '#7d5327' : '#936733' }}" stroke-width="{{ $isSelected ? '4.6' : '3.8' }}" />
                            <circle cx="{{ $pos['x'] }}" cy="{{ $pos['y'] }}" r="24" fill="#efe1c7" stroke="#b1854f" stroke-width="2" />
                            <ellipse cx="{{ $pos['x'] - 8 }}" cy="{{ $pos['y'] - 9 }}" rx="6" ry="3.5" fill="rgba(255,255,255,.45)" />
                        @endif

                        <text x="{{ $pos['x'] }}" y="{{ $pos['y'] + 52 }}" text-anchor="middle" fill="{{ $isDead ? '#535d6d' : '#3b2b18' }}" font-size="11" font-family="Georgia, serif">{{ \Illuminate\Support\Str::limit($node['name'], 24, '...') }}</text>
                    @endforeach
                </svg>
            </div>
        </section>
    @endif
@endsection

