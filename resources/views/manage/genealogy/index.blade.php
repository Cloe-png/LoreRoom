@extends('manage.layout')

@section('title', 'Gestion - Arbre genealogique')
@section('header', 'Arbre genealogique')

@section('content')
    <style>
        .genealogy-dates-focus {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 8px 0 12px;
            align-items: center;
        }
        .genealogy-dates-focus .chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 700;
            letter-spacing: .02em;
            border: 1px solid rgba(90,66,35,.24);
            background: rgba(255,255,255,.68);
            color: #3e2b15;
        }
        .genealogy-dates-focus .chip.birth {
            background: rgba(225, 244, 255, .92);
            border-color: rgba(66, 132, 173, .35);
            color: #1f4f6c;
        }
        .genealogy-dates-focus .chip.death {
            background: rgba(255, 232, 232, .92);
            border-color: rgba(166, 85, 85, .35);
            color: #6b2a2a;
        }
        .genealogy-basic-wrap {
            border: 1px solid rgba(114,84,49,.22);
            border-radius: 12px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.82), rgba(252,248,237,.86)),
                radial-gradient(800px 360px at 50% 0%, rgba(188,170,132,.1), transparent 70%);
            padding: 10px;
        }
    </style>
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
        @php
            $selectedNode = $nodes->firstWhere('id', (int) $selectedId);
            $formatDate = function ($date) {
                if (empty($date)) {
                    return '--/--/----';
                }
                try {
                    return \Illuminate\Support\Carbon::parse($date)->format('d/m/Y');
                } catch (\Throwable $e) {
                    return '--/--/----';
                }
            };
            $focusName = (string) ($selectedNode['name'] ?? 'Personnage');
            $focusBirth = $formatDate($selectedNode['birth_date'] ?? null);
            $focusDeathRaw = $selectedNode['death_date'] ?? null;
            $focusIsDead = (($selectedNode['status'] ?? '') === 'mort');
            $focusDeath = empty($focusDeathRaw) ? ($focusIsDead ? '--/--/----' : 'En vie') : $formatDate($focusDeathRaw);
        @endphp
        <section class="panel">
            <p class="muted" style="margin-top:0;">
                Vue lisible: filiation verticale, fratrie en pointille horizontal.
            </p>
            <div class="genealogy-dates-focus">
                <span id="focus-name-chip" class="chip">{{ $focusName }}</span>
                <span id="focus-birth-chip" class="chip birth">Naissance: {{ $focusBirth }}</span>
                <span id="focus-death-chip" class="chip death">Mort: {{ $focusDeath }}</span>
            </div>
            <div id="genealogy-network-error" class="muted" style="display:none; margin:0 0 10px; padding:10px; border:1px solid rgba(130,60,60,.28); border-radius:8px; background:rgba(255,245,245,.7); color:#6d2a2a;"></div>
            <div class="genealogy-basic-wrap">
                <div id="genealogy-network" style="height:720px; border:1px dashed rgba(114,84,49,.25); border-radius:10px; background:rgba(255,255,255,.35);"></div>
            </div>
        </section>
    @endif

    @if(!$nodes->isEmpty())
        @php
            $graphNodes = $nodes->map(function ($node) use ($selectedId) {
                return [
                    'id' => (int) $node['id'],
                    'label' => (string) $node['name'],
                    'generation' => (string) ($node['generation'] ?? ''),
                    'birth_date' => (string) ($node['birth_date'] ?? ''),
                    'death_date' => (string) ($node['death_date'] ?? ''),
                    'father_id' => (int) ($node['father_id'] ?? 0),
                    'mother_id' => (int) ($node['mother_id'] ?? 0),
                    'is_dead' => (($node['status'] ?? '') === 'mort'),
                    'is_selected' => ((int) $node['id'] === (int) $selectedId),
                    'image' => !empty($node['image_path']) ? route('media.show', ['path' => $node['image_path']], false) : null,
                    'level' => (int) ($node['level'] ?? 0),
                ];
            })->values();
        @endphp

        <script src="https://unpkg.com/vis-network@9.1.9/dist/vis-network.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/vis-network@9.1.9/dist/vis-network.min.js"></script>
        <script>
            (function () {
                const root = document.getElementById('genealogy-network');
                const errorBox = document.getElementById('genealogy-network-error');
                function showGraphError(message) {
                    if (!errorBox) return;
                    errorBox.style.display = 'block';
                    errorBox.textContent = message;
                }
                if (!root) return;
                if (typeof vis === 'undefined') {
                    showGraphError("Le module d'affichage du graphe n'a pas charge (vis-network). Recharge la page ou verifie la connexion internet.");
                    return;
                }

                try {
                    const rawNodes = @json($graphNodes);
                    const rawEdges = @json($edges->values());
                    const rawNodesById = new Map(rawNodes.map((n) => [Number(n.id), n]));
                    const twinNodeIds = new Set(
                        rawEdges
                            .filter((e) => (e.kind || '') === 'sibling' && (e.sibling_kind || '') === 'twin')
                            .flatMap((e) => [e.from, e.to])
                    );
                    const focusNameChip = document.getElementById('focus-name-chip');
                    const focusBirthChip = document.getElementById('focus-birth-chip');
                    const focusDeathChip = document.getElementById('focus-death-chip');

                function formatDateFr(isoDate) {
                    if (!isoDate || typeof isoDate !== 'string') return '...';
                    const parts = isoDate.split('-');
                    if (parts.length !== 3) return '...';
                    const [yyyy, mm, dd] = parts;
                    if (!yyyy || !mm || !dd) return '...';
                    return `${dd}/${mm}/${yyyy}`;
                }

                function formatLifeDates(birthIso, deathIso, isDead) {
                    const birth = formatDateFr(birthIso);
                    const death = formatDateFr(deathIso);
                    const birthText = birth === '...' ? '--/--/----' : birth;
                    const deathText = death === '...'
                        ? (isDead ? '--/--/----' : 'En vie')
                        : death;
                    return {
                        birthText,
                        deathText,
                        line1: `Naissance: ${birthText}`,
                        line2: `Mort: ${deathText}`,
                    };
                }

                const nodes = rawNodes.map((n) => {
                    const life = formatLifeDates(n.birth_date, n.death_date, n.is_dead);
                    const isTwin = twinNodeIds.has(n.id);
                    const node = {
                        id: n.id,
                        label: n.label || 'Personnage',
                        title: `${n.label}\n${life.line1}\n${life.line2}`,
                        level: n.level,
                        font: {
                            face: 'Georgia',
                            color: n.is_dead ? '#495262' : '#2f2a23',
                            size: 17,
                            multi: true,
                            align: 'center',
                        },
                        borderWidth: n.is_selected ? 3 : (isTwin ? 3 : 2),
                        color: n.is_dead
                            ? { background: '#edf1f6', border: '#70798a' }
                            : (isTwin ? { background: '#fff4df', border: '#bb8a3a' } : { background: '#f8f7f2', border: '#4e4e4e' }),
                        margin: { top: 8, right: 14, bottom: 8, left: 14 },
                        shape: 'box',
                        widthConstraint: { minimum: 140, maximum: 220 },
                        heightConstraint: { minimum: 42 },
                    };

                    return node;
                });

                const lineageStyle = {
                    arrows: '',
                    label: '',
                    width: 2.2,
                    color: { color: 'rgba(56,56,56,.95)' },
                    smooth: { enabled: true, type: 'cubicBezier', forceDirection: 'vertical', roundness: 0.12 },
                };
                const siblingStyle = {
                    arrows: '',
                    label: '',
                    dashes: [5, 4],
                    width: 2.4,
                    color: { color: 'rgba(80,70,55,.78)' },
                    smooth: false,
                };
                const twinStyle = {
                    arrows: '',
                    label: '',
                    dashes: [8, 5],
                    width: 2.6,
                    color: { color: 'rgba(166,120,49,.92)' },
                    smooth: false,
                };
                const coupleStyle = {
                    arrows: '',
                    label: '',
                    dashes: [9, 6],
                    width: 2.2,
                    color: { color: 'rgba(66,66,66,.9)' },
                    smooth: false,
                };

                const nodeIdSet = new Set(rawNodes.map((n) => n.id));
                const rawNodeById = new Map(rawNodes.map((n) => [n.id, n]));
                const visNodes = [...nodes];
                const visEdges = [];
                const coupleKeys = new Set();
                const unionKeys = new Set();

                rawNodes.forEach((child) => {
                    const fatherId = Number(child.father_id || 0);
                    const motherId = Number(child.mother_id || 0);
                    const hasFather = fatherId > 0 && nodeIdSet.has(fatherId);
                    const hasMother = motherId > 0 && nodeIdSet.has(motherId);

                    if (hasFather && hasMother) {
                        const a = Math.min(fatherId, motherId);
                        const b = Math.max(fatherId, motherId);
                        const coupleKey = `${a}-${b}`;
                        const unionId = `union-${coupleKey}`;

                        if (!coupleKeys.has(coupleKey)) {
                            visEdges.push({ from: a, to: b, ...coupleStyle });
                            coupleKeys.add(coupleKey);
                        }

                        if (!unionKeys.has(coupleKey)) {
                            const fatherLevel = Number(rawNodeById.get(fatherId)?.level ?? child.level);
                            const motherLevel = Number(rawNodeById.get(motherId)?.level ?? child.level);
                            const unionLevel = Math.min(fatherLevel, motherLevel);
                            visNodes.push({
                                id: unionId,
                                label: '',
                                shape: 'dot',
                                size: 1,
                                color: { background: 'rgba(0,0,0,0)', border: 'rgba(0,0,0,0)' },
                                borderWidth: 0,
                                physics: false,
                                level: unionLevel,
                            });
                            visEdges.push({ from: fatherId, to: unionId, ...lineageStyle });
                            visEdges.push({ from: motherId, to: unionId, ...lineageStyle });
                            unionKeys.add(coupleKey);
                        }

                        visEdges.push({ from: unionId, to: child.id, ...lineageStyle });
                        return;
                    }

                    if (hasFather) {
                        visEdges.push({ from: fatherId, to: child.id, ...lineageStyle });
                    } else if (hasMother) {
                        visEdges.push({ from: motherId, to: child.id, ...lineageStyle });
                    }
                });

                // Add explicit couple links coming from DB (spouse links), including couples without children.
                rawEdges.forEach((e) => {
                    if ((e.kind || '') !== 'couple') return;
                    const a = Number(e.from || 0);
                    const b = Number(e.to || 0);
                    if (!a || !b || !nodeIdSet.has(a) || !nodeIdSet.has(b)) return;
                    const key = `${Math.min(a, b)}-${Math.max(a, b)}`;
                    if (coupleKeys.has(key)) return;
                    visEdges.push({ from: a, to: b, ...coupleStyle });
                    coupleKeys.add(key);
                });

                // Sibling links are intentionally hidden to keep the chart clean like a classic pedigree.

                const data = { nodes: new vis.DataSet(visNodes), edges: new vis.DataSet(visEdges) };
                const options = {
                        layout: {
                            hierarchical: {
                                enabled: true,
                                direction: 'UD',
                                sortMethod: 'directed',
                                nodeSpacing: 190,
                                levelSeparation: 150,
                                treeSpacing: 190,
                                edgeMinimization: true,
                                parentCentralization: true,
                            },
                        },
                        physics: false,
                        interaction: { hover: true, navigationButtons: true, keyboard: true },
                        nodes: { shapeProperties: { useBorderWithImage: true } },
                };

                    const network = new vis.Network(root, data, options);

                    function updateFocusChips(nodeId) {
                        const raw = rawNodesById.get(Number(nodeId));
                        if (!raw) return;
                        const life = formatLifeDates(raw.birth_date, raw.death_date, raw.is_dead);
                        if (focusNameChip) focusNameChip.textContent = raw.label || 'Personnage';
                        if (focusBirthChip) focusBirthChip.textContent = `Naissance: ${life.birthText}`;
                        if (focusDeathChip) focusDeathChip.textContent = `Mort: ${life.deathText}`;
                    }

                    network.on('click', function (params) {
                        if (!params.nodes || params.nodes.length === 0) return;
                        const clickedId = params.nodes[0];
                        if (!rawNodesById.has(Number(clickedId))) return;
                        updateFocusChips(clickedId);
                    });
                } catch (error) {
                    showGraphError("Erreur JS dans l'arbre genealogique: " + (error && error.message ? error.message : "inconnue"));
                    console.error(error);
                }
            })();
        </script>
    @endif
@endsection
