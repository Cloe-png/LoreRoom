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
        <h3 style="margin-top:0;">Vue reseau (interactive)</h3>
        @if($graphRelations->isEmpty())
            <p class="muted">Aucune relation a afficher.</p>
        @else
            <div id="relations-network" style="height:560px; border:1px dashed rgba(114,84,49,.35); border-radius:8px; background:rgba(255,255,255,.25);"></div>
            @php
                $networkEdges = $graphRelations->values();
                $networkNodes = [];
                foreach ($graphRelations as $edge) {
                    if (!empty($edge['from_id']) && !isset($networkNodes[(int) $edge['from_id']])) {
                        $networkNodes[(int) $edge['from_id']] = [
                            'id' => (int) $edge['from_id'],
                            'label' => (string) ($edge['from'] ?? ('#' . $edge['from_id'])),
                            'is_dead' => (($edge['from_status'] ?? null) === 'mort'),
                            'image' => !empty($edge['from_photo']) ? route('media.show', ['path' => $edge['from_photo']], false) : null,
                        ];
                    }
                    if (!empty($edge['to_id']) && !isset($networkNodes[(int) $edge['to_id']])) {
                        $networkNodes[(int) $edge['to_id']] = [
                            'id' => (int) $edge['to_id'],
                            'label' => (string) ($edge['to'] ?? ('#' . $edge['to_id'])),
                            'is_dead' => (($edge['to_status'] ?? null) === 'mort'),
                            'image' => !empty($edge['to_photo']) ? route('media.show', ['path' => $edge['to_photo']], false) : null,
                        ];
                    }
                }
            @endphp
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
                    $fromPhoto = $relation->from_photo ?? null;
                    $toPhoto = $relation->to_photo ?? null;
                @endphp
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            @if(!empty($fromPhoto))
                                <img src="{{ route('media.show', ['path' => $fromPhoto], false) }}" alt="Photo source" style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:1px solid rgba(101,74,42,.35);">
                            @endif
                            <span>{{ optional($from)->display_name ?: '-' }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            @if(!empty($toPhoto))
                                <img src="{{ route('media.show', ['path' => $toPhoto], false) }}" alt="Photo cible" style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:1px solid rgba(101,74,42,.35);">
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

    @if(!$graphRelations->isEmpty())
        <script src="https://unpkg.com/vis-network@9.1.9/dist/vis-network.min.js"></script>
        <script>
            (function () {
                const root = document.getElementById('relations-network');
                if (!root || typeof vis === 'undefined') return;

                const rawNodes = @json(array_values($networkNodes));
                const rawEdges = @json($networkEdges);

                const nodes = rawNodes.map((n) => {
                    const node = {
                        id: n.id,
                        label: n.label,
                        font: { face: 'Georgia', color: n.is_dead ? '#4e5665' : '#3a2a17', size: 16, strokeWidth: 0 },
                        borderWidth: 3,
                        color: n.is_dead
                            ? { background: '#bcc3d0', border: '#5b6574' }
                            : { background: '#f3dfbd', border: '#8a612f' },
                    };

                    if (n.image) {
                        node.shape = 'circularImage';
                        node.image = n.image;
                        node.size = 30;
                        node.brokenImage = undefined;
                    } else {
                        node.shape = 'dot';
                        node.size = 28;
                    }

                    return node;
                });

                const edges = rawEdges.map((e) => ({
                    from: e.from_id,
                    to: e.to_id,
                    arrows: e.bidirectional ? '' : 'to',
                    label: e.type || '',
                    font: { face: 'Georgia', color: '#4f3b21', size: 13, strokeWidth: 0, align: 'top' },
                    color: { color: 'rgba(67,93,125,.82)' },
                    width: 2.8,
                    smooth: false,
                }));

                const data = { nodes: new vis.DataSet(nodes), edges: new vis.DataSet(edges) };
                const options = {
                    physics: {
                        enabled: true,
                        stabilization: { iterations: 180, fit: true },
                        barnesHut: { springLength: 150, springConstant: 0.03, damping: 0.28 },
                    },
                    interaction: { hover: true, navigationButtons: true, keyboard: true },
                    nodes: { shapeProperties: { useBorderWithImage: true } },
                    edges: { selectionWidth: 0 },
                };

                new vis.Network(root, data, options);
            })();
        </script>
    @endif
@endsection
