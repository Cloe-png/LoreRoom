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
            <div class="field" style="margin:0; min-width:min(320px, 100%);">
                <label>Personnage pivot</label>
                <select name="character_id">
                    <option value="">Tous</option>
                    @foreach($characters as $character)
                        <option value="{{ $character->id }}" {{ (int)($selectedCharacterId ?? 0) === (int)$character->id ? 'selected' : '' }}>
                            {{ $character->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button class="btn" type="submit">Rechercher</button>
            @if(!empty($q) || !empty($selectedCharacterId))
                <a class="btn secondary" href="{{ route('manage.relations.index') }}">Effacer</a>
            @endif
        </form>
    </section>

    <section class="panel">
        <h3 style="margin-top:0;">Vue réseau</h3>
        @if($graphRelations->isEmpty())
            <p class="muted">Aucune relation à afficher.</p>
        @else
            <div id="relations-network" style="height:680px; border:1px dashed rgba(114,84,49,.35); border-radius:8px; background:rgba(255,255,255,.25);"></div>
            @php
                $networkEdges = $graphRelations->values();
                $networkNodes = [];
                foreach ($graphRelations as $edge) {
                    if (!empty($edge['from_id']) && !isset($networkNodes[(int) $edge['from_id']])) {
                        $networkNodes[(int) $edge['from_id']] = [
                            'id' => (int) $edge['from_id'],
                            'label' => (string) ($edge['from'] ?? ('#' . $edge['from_id'])),
                            'is_dead' => (($edge['from_status'] ?? null) === 'mort'),
                            'preferred_color' => (string) ($edge['from_color'] ?? ''),
                            'image' => !empty($edge['from_photo']) ? route('media.show', ['path' => $edge['from_photo']], false) : null,
                        ];
                    }
                    if (!empty($edge['to_id']) && !isset($networkNodes[(int) $edge['to_id']])) {
                        $networkNodes[(int) $edge['to_id']] = [
                            'id' => (int) $edge['to_id'],
                            'label' => (string) ($edge['to'] ?? ('#' . $edge['to_id'])),
                            'is_dead' => (($edge['to_status'] ?? null) === 'mort'),
                            'preferred_color' => (string) ($edge['to_color'] ?? ''),
                            'image' => !empty($edge['to_photo']) ? route('media.show', ['path' => $edge['to_photo']], false) : null,
                        ];
                    }
                }
            @endphp
        @endif
    </section>

    @if(!$graphRelations->isEmpty())
        <script src="https://unpkg.com/vis-network@9.1.9/dist/vis-network.min.js"></script>
        <script>
            (function () {
                const root = document.getElementById('relations-network');
                if (!root || typeof vis === 'undefined') return;

                const rawNodes = @json(array_values($networkNodes));
                const rawEdges = @json($networkEdges);

                function compactLabel(name) {
                    const value = String(name || '').trim();
                    if (value === '') return '-';
                    const parts = value.split(/\s+/);
                    if (parts.length <= 1) return value;
                    return `${parts[0]}\n${parts.slice(1).join(' ')}`;
                }

                function hexToRgba(hex, alpha) {
                    const value = String(hex || '').trim();
                    if (!/^#[0-9A-Fa-f]{6}$/.test(value)) return null;
                    const r = parseInt(value.slice(1, 3), 16);
                    const g = parseInt(value.slice(3, 5), 16);
                    const b = parseInt(value.slice(5, 7), 16);
                    return `rgba(${r},${g},${b},${alpha})`;
                }

                const nodes = rawNodes.map((n) => {
                    const preferred = /^#[0-9A-Fa-f]{6}$/.test(String(n.preferred_color || ''))
                        ? String(n.preferred_color)
                        : '#8A612F';
                    const livingBackground = hexToRgba(preferred, 0.26) || '#f3dfbd';
                    const deadBackground = '#bcc3d0';
                    const deadBorder = '#5b6574';
                    const node = {
                        id: n.id,
                        label: compactLabel(n.label),
                        title: n.label,
                        font: { face: 'Georgia', color: n.is_dead ? '#4e5665' : '#3a2a17', size: 12, strokeWidth: 0, multi: true, align: 'center' },
                        borderWidth: 3,
                        color: n.is_dead
                            ? { background: deadBackground, border: deadBorder }
                            : { background: livingBackground, border: preferred },
                    };

                    if (n.image) {
                        node.shape = 'circularImage';
                        node.image = n.image;
                        node.size = 26;
                        node.brokenImage = undefined;
                    } else {
                        node.shape = 'dot';
                        node.size = 20;
                    }

                    return node;
                });

                // Deterministic circular layout to avoid node/label collisions.
                const count = Math.max(1, nodes.length);
                const radius = Math.max(260, 38 * count);
                nodes.forEach((node, index) => {
                    const angle = (index / count) * Math.PI * 2;
                    node.x = Math.round(Math.cos(angle) * radius);
                    node.y = Math.round(Math.sin(angle) * radius);
                    node.fixed = { x: true, y: true };
                });

                // Group duplicate links to avoid text/arrow overload.
                const grouped = new Map();
                rawEdges.forEach((e) => {
                    const from = Number(e.from_id || 0);
                    const to = Number(e.to_id || 0);
                    if (!from || !to || from === to) return;
                    const key = `${from}->${to}`;
                    if (!grouped.has(key)) {
                        grouped.set(key, {
                            from,
                            to,
                            bidirectional: !!e.bidirectional,
                            types: new Set(),
                            count: 0,
                            sourceColor: /^#[0-9A-Fa-f]{6}$/.test(String(e.from_color || '')) ? String(e.from_color) : '#435d7d',
                        });
                    }
                    const row = grouped.get(key);
                    row.bidirectional = row.bidirectional || !!e.bidirectional;
                    if (e.type) row.types.add(String(e.type));
                    if (!row.sourceColor && /^#[0-9A-Fa-f]{6}$/.test(String(e.from_color || ''))) {
                        row.sourceColor = String(e.from_color);
                    }
                    row.count += 1;
                });

                const hasReverse = (from, to) => grouped.has(`${to}->${from}`);
                const edges = Array.from(grouped.values()).map((e) => {
                    const reverse = hasReverse(e.from, e.to);
                    const smooth = reverse
                        ? { enabled: true, type: 'curvedCW', roundness: 0.24 }
                        : { enabled: true, type: 'continuous', roundness: 0.18 };
                    const title = e.types.size > 0 ? Array.from(e.types).join(' | ') : 'Relation';
                    return {
                        from: e.from,
                        to: e.to,
                        arrows: e.bidirectional
                            ? { from: { enabled: true, scaleFactor: 0.58 }, to: { enabled: true, scaleFactor: 0.58 } }
                            : { to: { enabled: true, scaleFactor: 0.72 } },
                        // Keep canvas readable: types are shown on hover only.
                        label: '',
                        title,
                        font: { face: 'Georgia', color: '#4f3b21', size: 11, strokeWidth: 0, align: 'top' },
                        color: { color: hexToRgba(e.sourceColor, 0.74) || 'rgba(67,93,125,.74)' },
                        width: Math.min(4, 1.8 + (e.count * 0.45)),
                        smooth,
                    };
                });

                const data = { nodes: new vis.DataSet(nodes), edges: new vis.DataSet(edges) };
                const options = {
                    physics: false,
                    interaction: { hover: true, navigationButtons: true, keyboard: true, tooltipDelay: 80 },
                    nodes: { shapeProperties: { useBorderWithImage: true } },
                    edges: { selectionWidth: 0, hoverWidth: 0.4 },
                };

                const network = new vis.Network(root, data, options);
                network.once('stabilized', function () {
                    network.fit({
                        animation: false,
                        padding: { top: 80, right: 80, bottom: 100, left: 80 },
                    });
                });
            })();
        </script>
    @endif
@endsection
