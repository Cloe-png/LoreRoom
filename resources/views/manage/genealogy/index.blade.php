@extends('manage.layout')

@section('title', 'Gestion - Arbre généalogique')
@section('header', 'Arbre généalogique')

@section('content')
    <style>
        :root { --paper:#f1e6cc; --ink:#2b1d14; --soft:#5a4532; --accent:#6b1e2a; --gold:#a8842f; --leather:#1b1511; }
        .genealogy-dates-focus { display:block; margin:0; padding:7px 0; border-top:1px solid #c7ac72; border-bottom:1px solid #c7ac72; color:var(--soft); font:600 11px/1.5 "Cinzel",serif; letter-spacing:.12em; font-variant-numeric:lining-nums tabular-nums; }
        .genealogy-dates-focus .chip { display:none; }
        .genealogy-basic-wrap { border:1px solid #9a7f48; outline:1px solid #c7ac72; outline-offset:-5px; border-radius:3px; background:rgba(255,250,236,.55); padding:10px; box-shadow:2px 3px 0 rgba(70,45,20,.22); }
        .genealogy-basic-wrap { position:relative; }
        #genealogy-network svg[role="tree"] g[data-node]:focus-visible > rect:nth-of-type(2) { stroke:#6b1e2a!important; stroke-width:3!important; }
        .genealogy-toolbar-card { padding:10px 16px!important; margin:0 0 10px!important; border-radius:3px!important; }
        .genealogy-toolbar-card form { display:grid; grid-template-columns:minmax(180px,1.2fr) minmax(160px,1fr) auto; gap:12px; align-items:end; margin:0; }
        .genealogy-toolbar-card .field { margin:0; }
        .genealogy-toolbar-card .genealogy-select-note { display:none; }
        .genealogy-toolbar-card .genealogy-toolbar { margin:8px 0 0; display:flex; gap:12px; align-items:center; }
        .genealogy-toolbar-card .genealogy-toolbar-group { gap:8px; }
        .genealogy-toolbar-card .genealogy-toolbar-group:last-child { margin-left:auto; }
        .genealogy-export-menu { position:relative; }
        .genealogy-export-menu > summary { list-style:none; cursor:pointer; min-height:32px; padding:7px 12px; border:1px solid var(--accent); border-radius:2px; color:var(--accent); background:transparent; font:600 11px/1.2 "Cinzel",serif; letter-spacing:.12em; }
        .genealogy-export-menu > summary::-webkit-details-marker { display:none; }
        .genealogy-export-menu[open] .genealogy-export-options { position:absolute; z-index:5; right:0; top:calc(100% + 4px); display:grid; min-width:150px; padding:5px; border:1px solid #9a7f48; background:var(--paper); box-shadow:2px 3px 0 rgba(40,15,10,.25); }
        .genealogy-export-options button { border:0!important; box-shadow:none!important; text-align:left; }
        .genealogy-help { display:inline-grid; place-items:center; width:20px; height:20px; border:1px solid #9a7f48; border-radius:50%; color:var(--soft); font:700 12px "Cinzel",serif; cursor:help; }
        @media (pointer:coarse) { .genealogy-help { display:none; } .genealogy-stage-note { display:block; margin:0; color:var(--soft); font-size:14px; } }
        .genealogy-toolbar .toggle { border:0; border-radius:0; padding:2px 0; background:transparent; color:var(--soft); font:600 11px/1.3 "Cinzel",serif; }
        .genealogy-toolbar .toggle input { appearance:none; width:18px; height:18px; border:1px solid #9a7f48; border-radius:2px; background:var(--paper); }
        .genealogy-toolbar .toggle input:checked { background:var(--accent); box-shadow:inset 0 0 0 3px var(--paper); }
        .genealogy-legend-floating { position:static; z-index:auto; display:flex; flex-wrap:wrap; gap:10px; margin:0 0 0 auto; padding:0; border:0; border-radius:0; background:transparent; box-shadow:none; color:var(--soft); font:600 10px/1.3 "Cinzel",serif; }
        .genealogy-legend-floating .line { width:24px; border-color:var(--soft); border-width:1.5px; }
        .genealogy-legend-floating .dot { width:11px; height:11px; border:1px solid #9a7f48; }
        .genealogy-legend-floating .dot.vivant { background:rgba(255,250,236,.85); }
        .genealogy-legend-floating .dot.mort { background:#ebdcb8; }
        .genealogy-legend-floating .hint { display:none; }
        .genealogy-stage-note { display:none; }
        #genealogy-network { height:calc(100vh - 250px)!important; min-height:600px!important; border:0!important; border-radius:0!important; background:rgba(241,230,204,.28)!important; }
        .genealogy-controls { position:absolute; right:18px; top:12px; display:flex; gap:4px; padding:0; border:0; border-radius:0; background:transparent; box-shadow:none; }
        .genealogy-controls button { min-width:32px; height:32px; border:1px solid var(--accent); border-radius:2px; background:var(--paper); color:var(--accent); font:600 12px "Cinzel",serif; }
        .genealogy-controls button:hover { background:#ead9b7; }
        .genealogy-toolbar-card :focus-visible,#genealogy-network :focus-visible { outline:2px solid var(--accent); outline-offset:3px; }
        @media (max-width: 820px) {
            .genealogy-toolbar-card form { grid-template-columns:1fr 1fr; }
            .genealogy-toolbar-card .genealogy-toolbar { flex-wrap:wrap; }
            .genealogy-legend-floating { margin:0; }
            #genealogy-network { height:calc(100vh - 330px)!important; min-height:600px!important; }
        }
        /* These page-specific rules follow the shared genealogy defaults. */
        .genealogy-dates-focus { display:block!important; margin:0!important; padding:7px 0!important; border-top:1px solid #c7ac72!important; border-bottom:1px solid #c7ac72!important; color:var(--soft)!important; font:600 11px/1.5 "Cinzel",serif!important; letter-spacing:.12em!important; font-variant-numeric:lining-nums tabular-nums; }
        .genealogy-dates-focus .chip { display:none!important; }
        .genealogy-legend-floating { position:static!important; z-index:auto!important; margin:0 0 0 auto!important; padding:0!important; border:0!important; border-radius:0!important; background:transparent!important; box-shadow:none!important; color:var(--soft)!important; font:600 10px/1.3 "Cinzel",serif!important; }
        .genealogy-controls { position:absolute!important; right:18px!important; top:12px!important; padding:0!important; border:0!important; border-radius:0!important; background:transparent!important; box-shadow:none!important; }
        .genealogy-controls button { min-width:32px!important; height:32px!important; border:1px solid var(--accent)!important; border-radius:2px!important; background:var(--paper)!important; color:var(--accent)!important; font:600 12px "Cinzel",serif!important; }
        .genealogy-basic-wrap { position:relative!important; border:1px solid #9a7f48!important; outline:1px solid #c7ac72!important; outline-offset:-5px!important; border-radius:3px!important; background:rgba(255,250,236,.55)!important; padding:10px!important; box-shadow:2px 3px 0 rgba(70,45,20,.22)!important; }
        .genealogy-legend-floating .line { width:24px!important; border-color:var(--soft)!important; border-width:1.5px!important; }
        .genealogy-legend-floating .dot { width:11px!important; height:11px!important; border:1px solid #9a7f48!important; }
        .genealogy-legend-floating .dot.vivant { background:rgba(255,250,236,.85)!important; }
        .genealogy-legend-floating .dot.mort { background:#ebdcb8!important; }
        #genealogy-network { height:calc(100vh - 250px)!important; min-height:600px!important; border:0!important; border-radius:0!important; background:rgba(241,230,204,.28)!important; }
        @media (max-width:820px) { #genealogy-network { height:calc(100vh - 330px)!important; min-height:600px!important; } }
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
            position: relative;
        }
        .genealogy-stage-note {
            margin: 0 0 10px;
            color: #6a5133;
            font-size: .9rem;
        }
        .genealogy-summary {
            display: grid;
            gap: 12px;
            grid-template-columns: 1.3fr .9fr;
            margin: 0 0 14px;
        }
        .genealogy-summary-card {
            border: 1px solid rgba(114,84,49,.2);
            border-radius: 14px;
            padding: 14px 16px;
            background: rgba(255,255,255,.54);
            box-shadow: 0 10px 24px rgba(67, 45, 20, .08);
        }
        .genealogy-summary-card h3 {
            margin: 0 0 6px;
            color: #4c3419;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1rem;
            letter-spacing: .04em;
        }
        .genealogy-summary-card p {
            margin: 0;
            color: #6d5234;
            line-height: 1.45;
        }
        .genealogy-stats {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .genealogy-stat {
            border-radius: 12px;
            padding: 12px;
            background: linear-gradient(180deg, rgba(255,250,239,.88), rgba(241,231,211,.9));
            border: 1px solid rgba(114,84,49,.18);
        }
        .genealogy-stat strong {
            display: block;
            color: #4a3117;
            font-size: 1.4rem;
            font-family: "Cinzel", "Times New Roman", serif;
        }
        .genealogy-stat span {
            color: #71563a;
            font-size: .86rem;
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .genealogy-controls {
            position: absolute;
            right: 18px;
            top: 18px;
            z-index: 3;
            display: flex;
            gap: 6px;
            background: rgba(255,255,255,.85);
            border: 1px solid rgba(80,80,80,.2);
            border-radius: 10px;
            padding: 6px;
            box-shadow: 0 6px 14px rgba(0,0,0,.12);
        }
        .genealogy-controls button {
            border: 1px solid rgba(60,60,60,.3);
            background: #fafafa;
            color: #222;
            min-width: 34px;
            height: 30px;
            border-radius: 7px;
            font-weight: 700;
            cursor: pointer;
        }
        .genealogy-controls button:hover {
            background: #f0f3fa;
        }
        .genealogy-legend-floating {
            position: fixed;
            right: 14px;
            bottom: 14px;
            z-index: 50;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border: 1px solid rgba(80,80,80,.2);
            border-radius: 10px;
            background: rgba(255,255,255,.9);
            box-shadow: 0 10px 24px rgba(0,0,0,.18);
            font-size: .9rem;
            color: #2b2b2b;
        }
        .genealogy-legend-floating .item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .genealogy-legend-floating .line {
            width: 34px;
            height: 0;
            border-top: 2px solid #2a2d33;
        }
        .genealogy-legend-floating .line.couple {
            border-top-style: dashed;
        }
        .genealogy-legend-floating .hint {
            color: #4d4d4d;
        }
        .genealogy-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin: 6px 0 12px;
        }
        .genealogy-toolbar-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .genealogy-toolbar .toggle {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid rgba(90,66,35,.24);
            background: rgba(255,255,255,.68);
            color: #3e2b15;
            font-size: .92rem;
        }
        .genealogy-toolbar .toggle input {
            margin: 0;
        }
        .genealogy-toolbar .btn {
            padding: 8px 10px;
        }
        .genealogy-legend-floating .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 1px solid rgba(35,35,35,.35);
            display: inline-block;
        }
        .genealogy-legend-floating .dot.vivant {
            background: #ffffff;
        }
        .genealogy-legend-floating .dot.mort {
            background: #f7dede;
        }
        .genealogy-select-note {
            margin: 0;
            color: #6d5234;
            font-size: .92rem;
            line-height: 1.45;
        }
        @media (max-width: 980px) {
            .genealogy-summary {
                grid-template-columns: 1fr;
            }
            .genealogy-stats {
                grid-template-columns: 1fr;
            }
            .genealogy-toolbar {
                align-items: stretch;
            }
            .genealogy-toolbar-group {
                width: 100%;
            }
            .genealogy-basic-wrap {
                padding: 8px;
            }
            .genealogy-controls {
                right: 12px;
                top: 12px;
            }
            .genealogy-legend-floating {
                position: static;
                margin-top: 12px;
            }
        }
    </style>
    <section class="panel genealogy-toolbar-card" style="margin-top:0;">
        <form method="GET" action="{{ route('manage.genealogy.index') }}" class="grid-4">
            <div class="field">
                <label>Personnage</label>
                <select name="focus_id">
                    @foreach($characters as $character)
                        <option value="{{ $character->id }}" {{ $selectedId == $character->id ? 'selected' : '' }}>{{ $character->display_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Lecture</label>
                <select name="view">
                    <option value="all" {{ ($viewMode ?? 'all') === 'all' ? 'selected' : '' }}>Arbre complet</option>
                    <option value="lineage" {{ ($viewMode ?? 'all') === 'lineage' ? 'selected' : '' }}>Lignée directe</option>
                    <option value="close" {{ ($viewMode ?? 'all') === 'close' ? 'selected' : '' }}>Famille proche</option>
                    <option value="paternal" {{ ($viewMode ?? 'all') === 'paternal' ? 'selected' : '' }}>Branche paternelle</option>
                    <option value="maternal" {{ ($viewMode ?? 'all') === 'maternal' ? 'selected' : '' }}>Branche maternelle</option>
                </select>
            </div>
            <div class="field" style="align-self:end;">
                <button class="btn" type="submit">Afficher l'arbre</button>
            </div>
        </form>
        <p class="genealogy-select-note">Passe en lecture ciblée quand l’univers devient dense: le but est de rendre l’histoire lisible, pas de tout montrer à la fois.</p>
        <div class="genealogy-toolbar">
            <div class="genealogy-toolbar-group"><label class="toggle"><input type="checkbox" id="tree-branch-highlight" checked> Mettre en avant la branche active</label><button type="button" class="btn secondary" id="tree-center-focus">Centrer la branche</button></div>
            <div class="genealogy-toolbar-group"><details class="genealogy-export-menu"><summary>Exporter ▾</summary><div class="genealogy-export-options"><button type="button" class="btn secondary" id="tree-export-png">Image PNG</button><button type="button" class="btn secondary" id="tree-export-jpg">Image JPG</button><button type="button" class="btn secondary" id="tree-export-pdf">PDF</button></div></details><span class="genealogy-help" tabindex="0" title="Faites glisser pour déplacer l’arbre. Molette pour zoomer." aria-label="Aide sur la navigation">?</span></div>
            <div class="genealogy-legend-floating"><span class="item"><span class="line"></span> Enfant</span><span class="item"><span class="line couple"></span> Couple</span><span class="item"><span class="line couple" style="border-top-style:dotted;"></span> Ex</span><span class="item"><span class="dot vivant"></span> Vivant</span><span class="item"><span class="dot mort"></span> Mort</span></div>
        </div>
    </section>
    <p id="genealogy-export-status" class="muted" role="status" aria-live="polite" hidden></p>

    @if($nodes->isEmpty())
        <section class="panel">
            <p class="muted">Aucune donnée généalogique disponible.</p>
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
            $viewModeLabel = 'Arbre complet';
            if (($viewMode ?? 'all') === 'lineage') {
                $viewModeLabel = 'Lignée directe';
            } elseif (($viewMode ?? 'all') === 'close') {
                $viewModeLabel = 'Famille proche';
            } elseif (($viewMode ?? 'all') === 'paternal') {
                $viewModeLabel = 'Branche paternelle';
            } elseif (($viewMode ?? 'all') === 'maternal') {
                $viewModeLabel = 'Branche maternelle';
            }
        @endphp
        <section class="panel">
            <div class="genealogy-dates-focus" id="genealogy-summary-line"><span id="focus-name-chip">{{ $focusName }}</span> · <span id="focus-life-chip">{{ $focusBirth }} – {{ $focusDeath }}</span> · <span id="focus-view-chip">{{ $viewModeLabel }}</span></div>
            <div id="genealogy-network-error" class="muted" style="display:none; margin:0 0 10px; padding:10px; border:1px solid rgba(130,60,60,.28); border-radius:8px; background:rgba(255,245,245,.7); color:#6d2a2a;"></div>
            <div class="genealogy-basic-wrap">
                <p class="genealogy-stage-note">Astuce mobile: fais glisser l’arbre pour suivre une branche sans perdre le contexte.</p>
                <div class="genealogy-controls">
                    <button type="button" id="tree-zoom-out" title="Zoom arrière">−</button>
                    <button type="button" id="tree-zoom-reset" title="Recentrer">1:1</button>
                    <button type="button" id="tree-zoom-in" title="Zoom +">+</button>
                </div>
                <div id="genealogy-network" style="touch-action:none;"></div>
            </div>
        </section>
    @endif

    @if(!$nodes->isEmpty())
        @php
            $graphNodes = $nodes->map(function ($node) use ($selectedId) {
                return [
                    'id' => (int) $node['id'],
                    'label' => (string) $node['name'],
                    'family_name' => (string) ($node['family_name'] ?? ''),
                    'generation' => (string) ($node['generation'] ?? ''),
                    'birth_date' => (string) ($node['birth_date'] ?? ''),
                    'death_date' => (string) ($node['death_date'] ?? ''),
                    'father_id' => (int) ($node['father_id'] ?? 0),
                    'mother_id' => (int) ($node['mother_id'] ?? 0),
                    'spouse_id' => (int) ($node['spouse_id'] ?? 0),
                    'status' => (string) ($node['status'] ?? ''),
                    'is_dead' => (($node['status'] ?? '') === 'mort'),
                    'is_ghost' => (bool) ($node['is_ghost'] ?? false),
                    'is_selected' => ((int) $node['id'] === (int) $selectedId),
                    'image' => !empty($node['image_path']) ? route('media.show', ['path' => $node['image_path']], false) : null,
                    'level' => (int) ($node['level'] ?? 0),
                ];
            })->values();
        @endphp

        <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
        <script src="{{ mix('js/image-export.js') }}" defer></script>
        <script>
            (function(){
                const root=document.getElementById('genealogy-network');
                const err=document.getElementById('genealogy-network-error');
                if(!root)return;
                const showErr=(m)=>{ if(err){ err.style.display='block'; err.textContent=m; } };
                const fmt=(d)=>{ if(!d||typeof d!=='string')return '...'; const p=d.split('-'); return p.length===3?`${p[2]}/${p[1]}/${p[0]}`:'...'; };
                const life=(n)=>({b:fmt(n.birth_date)==='...'?'--/--/----':fmt(n.birth_date),d:fmt(n.death_date)==='...'?((n.is_dead)?'--/--/----':'En vie'):fmt(n.death_date)});
                const S=(n,a={})=>{const e=document.createElementNS('http://www.w3.org/2000/svg',n);Object.entries(a).forEach(([k,v])=>e.setAttribute(k,String(v)));return e;};
                try{
                    const raw=@json($graphNodes)||[]; const edges=@json($edges->values())||[]; if(!raw.length)return;
                    const focusName=document.getElementById('focus-name-chip');
                    const focusLife=document.getElementById('focus-life-chip');
                    const focusView=document.getElementById('focus-view-chip');
                    const initialView=@json($viewModeLabel);
                    const min=Math.min(...raw.map(n=>Number(n.level||0)));
                    const nodes=raw.map(n=>({...n,id:Number(n.id),father_id:Number(n.father_id||0),mother_id:Number(n.mother_id||0),spouse_id:Number(n.spouse_id||0),level:Number(n.level||0)-min}));
                    const byId=new Map(nodes.map(n=>[n.id,n])); const ids=new Set(nodes.map(n=>n.id));
                    const selected=nodes.find(n=>!!n.is_selected) || nodes[0];
                    let currentFocusId=selected ? Number(selected.id) : 0;
                    const expandedExternalFamilies=new Set();

                    const cp=[]; const k=new Set(); const addPair=(a,b)=>{ const x=Number(a||0),y=Number(b||0); if(!x||!y||x===y||!ids.has(x)||!ids.has(y))return; const l=Math.min(x,y),r=Math.max(x,y),key=`${l}-${r}`; if(k.has(key))return; k.add(key); cp.push([l,r]); };
                    edges.forEach(e=>{ if((e.kind||'')!=='couple')return; addPair(e.from,e.to); });
                    nodes.forEach(n=>{ if(n.spouse_id&&ids.has(n.spouse_id)) addPair(n.id,n.spouse_id); });
                    nodes.forEach(ch=>{ const f=ch.father_id&&ids.has(ch.father_id)?ch.father_id:0, m=ch.mother_id&&ids.has(ch.mother_id)?ch.mother_id:0; if(f&&m) addPair(f,m); });
                    const exPairs=[];
                    edges.forEach(e=>{
                        if((e.kind||'')!=='ex') return;
                        const a=Number(e.from||0), b=Number(e.to||0);
                        if(!a || !b || a===b || !ids.has(a) || !ids.has(b)) return;
                        const l=Math.min(a,b), r=Math.max(a,b);
                        exPairs.push([l,r]);
                    });
                    const W=240,H=112,DY=190,PARTNER_GAP=40,SIBLING_GAP=16,COUSIN_GAP=64,BRANCH_GAP=120,PAD=40;
                    const levels=[...new Set(nodes.map(n=>n.level))].sort((a,b)=>a-b);
                    const pos=new Map();
                    const familyKeyFor=(node)=>{
                        const parents=[Number(node.father_id||0),Number(node.mother_id||0)].filter(id=>id&&ids.has(id)).sort((a,b)=>a-b);
                        return parents.length ? `parents:${parents.join('-')}` : `root:${node.id}`;
                    };
                    const partnerLinks=[...cp,...exPairs];
                    const partnerGraph=new Map();
                    partnerLinks.forEach(([a,b])=>{
                        if(!partnerGraph.has(a))partnerGraph.set(a,new Set());
                        if(!partnerGraph.has(b))partnerGraph.set(b,new Set());
                        partnerGraph.get(a).add(b); partnerGraph.get(b).add(a);
                    });
                    const compareBirthId=(a,b)=>{
                        const dateA=String(a.birth_date||'9999-12-31'), dateB=String(b.birth_date||'9999-12-31');
                        return dateA.localeCompare(dateB) || Number(a.id)-Number(b.id);
                    };
                    const unitWidth=(unit)=>unit.ids.length*W+Math.max(0,unit.ids.length-1)*PARTNER_GAP;
                    const buildUnits=(level)=>{
                        const remaining=new Set(nodes.filter(node=>node.level===level).map(node=>node.id));
                        const units=[];
                        while(remaining.size){
                            const seed=byId.get(remaining.values().next().value);
                            const component=new Set(), queue=[seed.id];
                            while(queue.length){
                                const id=queue.shift(); if(component.has(id)||!remaining.has(id))continue;
                                component.add(id); remaining.delete(id);
                                (partnerGraph.get(id)||new Set()).forEach(other=>{ if(remaining.has(other)&&byId.get(other)?.level===level)queue.push(other); });
                            }
                            const members=[...component].map(id=>byId.get(id)).filter(Boolean);
                            const ranked=[...members].sort((a,b)=>((partnerGraph.get(b.id)?.size||0)-(partnerGraph.get(a.id)?.size||0)) || (a.id===currentFocusId?-1:0) || compareBirthId(a,b));
                            const anchor=ranked[0]||seed;
                            const companions=members.filter(member=>member.id!==anchor.id).sort(compareBirthId);
                            const leftCount=Math.floor(companions.length/2);
                            const ordered=[...companions.slice(0,leftCount),anchor,...companions.slice(leftCount)];
                            units.push({ids:ordered.map(member=>member.id),anchorId:anchor.id,familyKey:familyKeyFor(anchor),members:ordered});
                        }
                        return units;
                    };

                    const getNodePreferredCenter=(id)=>{
                        const n=byId.get(id);
                        if(!n) return NaN;
                        const f=n.father_id&&pos.has(n.father_id)?n.father_id:0;
                        const m=n.mother_id&&pos.has(n.mother_id)?n.mother_id:0;
                        if(f&&m){
                            const fx=pos.get(f).x+W/2, mx=pos.get(m).x+W/2;
                            return (fx+mx)/2;
                        }
                        if(f||m){
                            const p=f||m;
                            return pos.get(p).x+W/2;
                        }
                        return NaN;
                    };

                    const siblingSet=new Set();
                    for(let i=0;i<nodes.length;i++)for(let j=i+1;j<nodes.length;j++){
                        const a=nodes[i],b=nodes[j];
                        const sharedFather=a.father_id&&a.father_id===b.father_id, sharedMother=a.mother_id&&a.mother_id===b.mother_id;
                        if(sharedFather||sharedMother)siblingSet.add(`${Math.min(a.id,b.id)}-${Math.max(a.id,b.id)}`);
                    }
                    const groupsByLevel=new Map();
                    levels.forEach(level=>{
                        const groups=new Map();
                        buildUnits(level).forEach(unit=>{
                            if(!groups.has(unit.familyKey))groups.set(unit.familyKey,{key:unit.familyKey,units:[],parentIds:[],pref:NaN});
                            const group=groups.get(unit.familyKey); group.units.push(unit);
                            const anchor=byId.get(unit.anchorId);
                            const parents=[Number(anchor?.father_id||0),Number(anchor?.mother_id||0)].filter(id=>id&&pos.has(id));
                            group.parentIds=[...new Set([...group.parentIds,...parents])];
                            const preferred=getNodePreferredCenter(unit.anchorId);
                            if(Number.isFinite(preferred))group.pref=Number.isFinite(group.pref)?(group.pref+preferred)/2:preferred;
                        });
                        const blocks=[...groups.values()];
                        blocks.forEach(block=>{
                            block.units.sort((a,b)=>compareBirthId(byId.get(a.anchorId),byId.get(b.anchorId)));
                            let unitOffset=0; const childCenters=[];
                            block.units.forEach(unit=>{ childCenters.push(unitOffset+unit.ids.indexOf(unit.anchorId)*(W+PARTNER_GAP)+W/2); unitOffset+=unitWidth(unit)+SIBLING_GAP; });
                            block.width=block.units.reduce((sum,unit)=>sum+unitWidth(unit),0)+Math.max(0,block.units.length-1)*SIBLING_GAP;
                            block.preferredOffset=childCenters.length?(childCenters[0]+childCenters[childCenters.length-1])/2:block.width/2;
                        });
                        blocks.sort((a,b)=>{
                            if(Number.isFinite(a.pref)!==Number.isFinite(b.pref))return Number.isFinite(a.pref)?-1:1;
                            if(Number.isFinite(a.pref)&&a.pref!==b.pref)return a.pref-b.pref;
                            return compareBirthId(byId.get(a.units[0]?.anchorId)||{},byId.get(b.units[0]?.anchorId)||{});
                        });
                        groupsByLevel.set(level,blocks);
                        let cursor=PAD;
                        blocks.forEach((block,index)=>{
                            const desired=Number.isFinite(block.pref)?block.pref-block.preferredOffset:cursor;
                            const left=Math.max(cursor,desired);
                            let x=left;
                            block.units.forEach(unit=>{
                                unit.ids.forEach(id=>{pos.set(id,{x,y:PAD+level*DY});x+=W+PARTNER_GAP;});
                                x-=PARTNER_GAP;
                                x+=SIBLING_GAP;
                            });
                            cursor=left+block.width;
                            if(index<blocks.length-1){
                                const next=blocks[index+1];
                                const cousins=block.parentIds.some(a=>next.parentIds.some(b=>siblingSet.has(`${Math.min(a,b)}-${Math.max(a,b)}`)));
                                cursor+=cousins?COUSIN_GAP:BRANCH_GAP;
                            }
                        });
                    });
                    const layoutValues=[...pos.values()];
                    const layoutMaxX=Math.max(...layoutValues.map(point=>point.x+W));
                    const layoutMaxY=Math.max(...layoutValues.map(point=>point.y+H));
                    const svgW=Math.max(root.clientWidth,layoutMaxX+PAD), svgH=Math.max(root.clientHeight,layoutMaxY+PAD);

                    const byBirthThenName=(a,b)=>{
                        const dA=String(a.birth_date||'9999-12-31');
                        const dB=String(b.birth_date||'9999-12-31');
                        return dA.localeCompare(dB) || Number(a.id||0)-Number(b.id||0);
                    };
                    const fam=new Map();
                    nodes.forEach(ch=>{ const parents=[Number(ch.father_id||0),Number(ch.mother_id||0)].filter(id=>id&&ids.has(id)).sort((a,b)=>a-b); if(!parents.length)return; const key=parents.join('-'); if(!fam.has(key))fam.set(key,{key,parents,children:[]}); fam.get(key).children.push(ch.id); });
                    fam.forEach((f)=>{ f.children=(f.children||[]).sort((a,b)=>byBirthThenName(byId.get(a)||{}, byId.get(b)||{})); });
                    const unionLanes=new Map();
                    const unionsByParent=new Map();
                    fam.forEach(union=>{ if(union.parents.length<2)return; union.parents.forEach(parentId=>{ if(!unionsByParent.has(parentId))unionsByParent.set(parentId,[]); unionsByParent.get(parentId).push(union); }); });
                    unionsByParent.forEach(unions=>{
                        unions.sort((a,b)=>{
                            const center=union=>union.parents.map(id=>pos.get(id)).filter(Boolean).reduce((sum,p)=>sum+p.x+W/2,0)/union.parents.length;
                            return center(a)-center(b);
                        });
                        unions.forEach((union,index)=>unionLanes.set(union.key,Math.max(unionLanes.get(union.key)||0,index)));
                    });

                    const svg=S('svg',{width:svgW,height:svgH,viewBox:`0 0 ${svgW} ${svgH}`,role:'tree','aria-label':'Arbre généalogique des personnages'});
                    const viewport=S('g');
                    const bg=S('g');
                    const eg=S('g');
                    const ng=S('g');
                    bg.setAttribute('aria-hidden','true');
                    eg.setAttribute('aria-hidden','true');
                    viewport.appendChild(bg);
                    viewport.appendChild(eg);
                    viewport.appendChild(ng);
                    svg.appendChild(viewport);
                    const branchHighlightToggle=document.getElementById('tree-branch-highlight');
                    const centerFocusBtn=document.getElementById('tree-center-focus');
                    const familyNameFor=(node)=>{
                        const raw=String(node.family_name || '').trim();
                        if(raw) return raw;
                        const parts=String(node.label || '').trim().split(/\s+/).filter(Boolean);
                        return parts.length > 1 ? parts[parts.length - 1] : 'Sans famille';
                    };
                    const routedSegments=[];
                    const drawLine=(x1,y1,x2,y2,kind,options={})=>{
                        const faded=!!options.faded;
                        const lineColor='#5a4532';
                        const lineOpacityBase=kind==='ex' ? 0.62 : 0.94;
                        const lineWidth=1.5;
                        const dash=kind==='ex' ? '6 4' : (kind==='sibling' ? '4 6' : '');
                        const opacity=faded ? Math.min(0.22, lineOpacityBase) : lineOpacityBase;
                        const attrs={
                            stroke:lineColor,
                            fill:'none',
                            'stroke-opacity':String(opacity),
                            'stroke-width':lineWidth,
                            'stroke-dasharray':dash,
                            'stroke-linecap':'round',
                            'stroke-linejoin':'round'
                        };

                        if(x1!==x2&&y1!==y2){ const midY=(y1+y2)/2; routedSegments.push([x1,y1,x1,midY],[x1,midY,x2,midY],[x2,midY,x2,y2]); eg.appendChild(S('path',{d:`M ${x1} ${y1} L ${x1} ${midY} L ${x2} ${midY} L ${x2} ${y2}`,...attrs})); return; }
                        routedSegments.push([x1,y1,x2,y2]);
                        eg.appendChild(S('line',{x1,y1,x2,y2,...attrs}));
                    };

                    const siblingPairs=edges
                        .filter(e => (e.kind || '') === 'sibling')
                        .map(e => [Number(e.from || 0), Number(e.to || 0)])
                        .filter(([a,b]) => a > 0 && b > 0 && a !== b && ids.has(a) && ids.has(b));

                    const shortLabel=(t,m)=>{ const s=String(t||'').trim(); return s.length>m?`${s.slice(0,m-1)}…`:s; };
                    const toNameParts=(value)=>{
                        const s=String(value||'').trim();
                        if(!s) return { first:'Personnage', last:'' };
                        const words=s.split(/\s+/).filter(Boolean);
                        if(words.length===1) return { first:words[0], last:'' };
                        return {
                            first:words[0],
                            last:words.slice(1).join(' ')
                        };
                    };
                    const initials=(parts)=>{
                        const a=String(parts.first||'').trim().charAt(0).toUpperCase();
                        const b=String(parts.last||'').trim().charAt(0).toUpperCase();
                        return `${a}${b}`.trim() || 'P';
                    };
                    const update=(id)=>{ const n=byId.get(Number(id)); if(!n)return; const l=life(n); if(focusName)focusName.textContent=n.label||'Personnage'; if(focusLife)focusLife.textContent=`${l.b} – ${l.d}`; if(focusView)focusView.textContent=initialView; };
                    const statusFill=(status, isGhost)=>{
                        const s=String(status||'').toLowerCase();
                        if(s==='mort') return '#ebdcb8';
                        return 'rgba(255,250,236,.85)';
                    };
                    const statusStroke=(status, isFocused, isGhost)=>{
                        if(isFocused) return '#6b1e2a';
                        const s=String(status||'').toLowerCase();
                        if(s==='mort') return '#8d6656';
                        return '#9a7f48';
                    };
                    const computeFocusSet=(id)=>{
                        const targetId=Number(id || 0);
                        if(!targetId || !byId.has(targetId)) return new Set(nodes.map(n=>n.id));
                        const focusSet=new Set([targetId]);
                        const walkAncestors=(nodeId)=>{
                            const node=byId.get(nodeId);
                            if(!node) return;
                            [Number(node.father_id || 0), Number(node.mother_id || 0)].forEach((parentId)=>{
                                if(parentId > 0 && byId.has(parentId) && !focusSet.has(parentId)){
                                    focusSet.add(parentId);
                                    walkAncestors(parentId);
                                }
                            });
                        };
                        const walkDescendants=(nodeId)=>{
                            nodes.forEach((node)=>{
                                if(Number(node.father_id || 0) === nodeId || Number(node.mother_id || 0) === nodeId){
                                    if(!focusSet.has(node.id)){
                                        focusSet.add(node.id);
                                        walkDescendants(node.id);
                                    }
                                }
                            });
                        };
                        walkAncestors(targetId);
                        walkDescendants(targetId);
                        cp.forEach(([a,b])=>{
                            if(focusSet.has(a) || focusSet.has(b)){
                                focusSet.add(a);
                                focusSet.add(b);
                            }
                        });
                        siblingPairs.forEach(([a,b])=>{
                            if(focusSet.has(a) || focusSet.has(b)){
                                focusSet.add(a);
                                focusSet.add(b);
                            }
                        });
                        return focusSet;
                    };
                    const renderGraph=()=>{
                        bg.innerHTML='';
                        eg.innerHTML='';
                        ng.innerHTML='';
                        routedSegments.length=0;
                        const visible=new Set(nodes.map(n => n.id));
                        const collectAncestors=(nodeId,target,visited=new Set())=>{
                            if(visited.has(nodeId))return; visited.add(nodeId);
                            const node=byId.get(nodeId); if(!node)return;
                            [Number(node.father_id||0),Number(node.mother_id||0)].forEach(parentId=>{if(parentId&&ids.has(parentId)){target.add(parentId);collectAncestors(parentId,target,visited);}});
                        };
                        const coreAncestors=new Set(); nodes.filter(node=>!node.is_ghost).forEach(node=>collectAncestors(node.id,coreAncestors));
                        const collapsedExternalAncestors=new Set(); nodes.filter(node=>node.is_ghost&&!expandedExternalFamilies.has(node.id)).forEach(node=>collectAncestors(node.id,collapsedExternalAncestors));
                        collapsedExternalAncestors.forEach(id=>{if(!coreAncestors.has(id))visible.delete(id);});
                        const branchHighlightActive=branchHighlightToggle ? branchHighlightToggle.checked : true;
                        const focusSet=branchHighlightActive ? computeFocusSet(currentFocusId) : new Set(nodes.map(n=>n.id));
                        cp.forEach(([a,b])=>{
                            if(!visible.has(a) || !visible.has(b)) return;
                            const A=pos.get(a),B=pos.get(b); if(!A||!B)return;
                        const L=A.x<=B.x?A:B,R=A.x<=B.x?B:A;
                            const isEx=exPairs.some(([left,right])=>left===Math.min(a,b)&&right===Math.max(a,b));
                            const midX=(L.x+W+R.x)/2, midY=L.y+H/2;
                            drawLine(L.x+W,midY,R.x,midY,isEx?'ex':'couple',{ faded: branchHighlightActive && (!focusSet.has(a) || !focusSet.has(b)) });
                            const mark=S('text',{x:midX,y:midY-8,'text-anchor':'middle','font-family':'Cinzel,serif','font-size':11,fill:'#a8842f','paint-order':'stroke','stroke':'#f1e6cc','stroke-width':4,'aria-hidden':'true'}); mark.textContent='⚭'; eg.appendChild(mark);
                            eg.appendChild(S('circle',{cx:midX,cy:midY,r:4,fill:'#a8842f',stroke:'#f1e6cc','stroke-width':1,'aria-hidden':'true'}));
                        });
                        edges
                            .filter(e => (e.kind || '') === 'ex')
                            .forEach(e => {
                                const a=Number(e.from||0), b=Number(e.to||0);
                                if(cp.some(([left,right])=>left===Math.min(a,b)&&right===Math.max(a,b))) return;
                                if(!visible.has(a) || !visible.has(b)) return;
                                const A=pos.get(a),B=pos.get(b); if(!A||!B)return;
                                const L=A.x<=B.x?A:B,R=A.x<=B.x?B:A;
                                drawLine(L.x+W,L.y+H/2,R.x,R.y+H/2,'ex',{ faded: branchHighlightActive && (!focusSet.has(a) || !focusSet.has(b)) });
                                eg.appendChild(S('circle',{cx:(L.x+W+R.x)/2,cy:L.y+H/2,r:4,fill:'#a8842f',stroke:'#f1e6cc','stroke-width':1,'aria-hidden':'true'}));
                            });

                        fam.forEach(f=>{
                            const parents=(f.parents || []).filter(id=>visible.has(id));
                            const children=(f.children || []).filter(id=>visible.has(id)&&(!byId.get(id)?.is_ghost||expandedExternalFamilies.has(id)));
                            if(!parents.length || !children.length) return;
                            const pp=parents.map(id=>pos.get(id)).filter(Boolean);
                            if(!pp.length) return;
                            const ch=children
                                .map(id=>({id,p:pos.get(id)}))
                                .filter(row=>!!row.p)
                                .sort((a,b)=>byBirthThenName(byId.get(a.id)||{}, byId.get(b.id)||{}))
                                ;
                            if(!ch.length) return;

                            const py=Math.max(...pp.map(p=>p.y+H));
                            const familyFocused=parents.some((id)=>focusSet.has(id)) || children.some((id)=>focusSet.has(id));
                            let unionX, unionY, routeY;
                            if(pp.length>=2){
                                const ordered=[...pp].sort((a,b)=>a.x-b.x), left=ordered[0], right=ordered[ordered.length-1];
                                unionX=(left.x+W+right.x)/2; unionY=left.y+H/2;
                                const lane=unionLanes.get(f.key)||0;
                                routeY=py+24+lane*14;
                                drawLine(unionX,unionY,unionX,routeY,'lineage',{faded:branchHighlightActive&&!familyFocused});
                            } else {
                                unionX=pp[0].x+W/2; unionY=py+12; routeY=py+24;
                                drawLine(unionX,py,unionX,unionY,'lineage',{faded:branchHighlightActive&&!familyFocused});
                                bg.appendChild(S('circle',{cx:unionX,cy:unionY,r:4,fill:'#a8842f','aria-hidden':'true'}));
                                drawLine(unionX,unionY,unionX,routeY,'lineage',{faded:branchHighlightActive&&!familyFocused});
                            }

                            const childXs=ch.map(row=>row.p.x+W/2), barLeft=Math.min(...childXs),barRight=Math.max(...childXs),barY=routeY+(ch.length>1?8:0),barCenter=(barLeft+barRight)/2;
                            drawLine(unionX,routeY,barCenter,routeY,'lineage',{faded:branchHighlightActive&&!familyFocused});
                            if(barY!==routeY)drawLine(barCenter,routeY,barCenter,barY,'lineage',{faded:branchHighlightActive&&!familyFocused});
                            if(ch.length>1)drawLine(barLeft,barY,barRight,barY,'lineage',{faded:branchHighlightActive&&!familyFocused});
                            ch.forEach(row=>drawLine(row.p.x+W/2,barY,row.p.x+W/2,row.p.y,'lineage',{faded:branchHighlightActive&&!focusSet.has(row.id)&&!familyFocused}));

                            const unionName=String(byId.get(ch[0].id)?.family_name||'').trim();
                            const parentNames=parents.map(id=>familyNameFor(byId.get(id)||{}).trim()).filter(Boolean);
                            if(unionName&&parentNames.length&&!parentNames.includes(unionName)){
                                const familyLabel=S('text',{x:unionX+8,y:py+17,'font-family':'Cinzel,serif','font-size':11,'letter-spacing':2.64,fill:'#5a4532','paint-order':'stroke','stroke':'#f1e6cc','stroke-width':4,'aria-hidden':'true'});
                                familyLabel.textContent=unionName.toLocaleUpperCase(); bg.appendChild(familyLabel);
                            }
                        });

                        nodes.forEach(n=>{
                            if(!visible.has(n.id)) return;
                            const p=pos.get(n.id); if(!p)return;
                            const isFocused=n.id===currentFocusId;
                            const isGhost=!!n.is_ghost;
                            const isDimmed=branchHighlightActive && !focusSet.has(n.id);
                            const g=S('g',{style:'cursor:pointer', 'data-node':'1',tabindex:'0',role:'treeitem','aria-label':String(n.label||'Personnage')});
                            g.setAttribute('opacity', isDimmed ? '0.24' : (isGhost ? '0.85' : '1'));
                            g.appendChild(S('rect',{x:p.x+3,y:p.y+3,width:W,height:H,rx:3,fill:'rgba(70,45,20,.22)','pointer-events':'none'}));
                            g.appendChild(S('rect',{
                                x:p.x,y:p.y,width:W,height:H,rx:3,
                                fill:statusFill(n.status, isGhost),
                                stroke:statusStroke(n.status, isFocused, isGhost),
                                'stroke-width':isFocused?2:1,
                                'stroke-dasharray':isGhost ? '6 6' : ''
                            }));
                            g.appendChild(S('rect',{x:p.x+3,y:p.y+3,width:W-6,height:H-6,rx:1,fill:'none',stroke:'#c7ac72','stroke-width':1,'pointer-events':'none'}));
                            const parts=toNameParts(String(n.label||'Personnage'));
                            const titleEl=S('title');
                            titleEl.textContent=String(n.label || `${parts.first || ''} ${parts.last || ''}`).trim();
                            g.appendChild(titleEl);
                            const cx=p.x+42, cy=p.y+(H/2), r=29;
                            const clipId=`genea-clip-${n.id}`;
                            const textClipId=`genea-text-clip-${n.id}`;
                            const defs=S('defs');
                            const clip=S('clipPath',{id:clipId});
                            clip.appendChild(S('circle',{cx,cy,r}));
                            const textClip=S('clipPath',{id:textClipId});
                            textClip.appendChild(S('rect',{x:p.x+74,y:p.y+12,width:W-86,height:H-24,rx:4}));
                            defs.appendChild(clip);
                            defs.appendChild(textClip);
                            g.appendChild(defs);
                            g.appendChild(S('circle',{
                                cx,cy,r:r+1.5,
                                fill:'#fffaf0',
                                stroke:isFocused?'#6b1e2a':'#9a7f48',
                                'stroke-width':isFocused?3:2,
                                'stroke-dasharray':isGhost ? '5 5' : ''
                            }));
                            if(n.image){
                                const img=S('image',{
                                    href:n.image,
                                    x:cx-r,
                                    y:cy-r,
                                    width:r*2,
                                    height:r*2,
                                    preserveAspectRatio:'xMidYMid slice',
                                    'clip-path':`url(#${clipId})`,
                                });
                                g.appendChild(img);
                            } else {
                                g.appendChild(S('circle',{cx,cy,r,fill:n.is_dead?'#ebdcb8':'#e8d9b8'}));
                                const init=S('text',{x:cx,y:cy+5,'text-anchor':'middle','font-family':'Cormorant Garamond,serif','font-size':'18',fill:'#2b1d14','font-weight':'700'});
                                init.textContent=initials(parts);
                                g.appendChild(init);
                            }
                            const fullName=`${n.is_dead?'† ':''}${parts.first}${parts.last ? ` ${parts.last}` : ''}`.trim();
                            const firstName=S('text',{
                                x:p.x+82,
                                y:p.y+39,
                                'font-family':'Cormorant Garamond, serif',
                                'font-size':'17',
                                fill:'#2b1d14',
                                'font-weight':'600',
                                'clip-path':`url(#${textClipId})`
                            });
                            firstName.textContent=`${n.is_dead?'† ':''}${parts.first}`;
                            g.appendChild(firstName);
                            const family=S('text',{x:p.x+82,y:p.y+61,'font-family':'Cinzel,serif','font-size':9,'letter-spacing':1.1,fill:'#5a4532','clip-path':`url(#${textClipId})`}); family.textContent=n.family_name || parts.last; g.appendChild(family);
                            const birthYear=String(n.birth_date||'').match(/^(\d{4})/)?.[1]||'';
                            const deathYear=String(n.death_date||'').match(/^(\d{4})/)?.[1]||'';
                            const lifeText=n.is_dead?(birthYear&&deathYear?`${birthYear} – ${deathYear}`:(birthYear?`Né(e) en ${birthYear}`:'')):(birthYear?`Né(e) en ${birthYear}`:'');
                            if(lifeText){ const dates=S('text',{x:p.x+82,y:p.y+88,'font-family':'Cormorant Garamond,serif','font-size':14,'font-style':'italic',fill:'#5a4532','font-variant-numeric':'lining-nums tabular-nums'}); dates.textContent=lifeText; g.appendChild(dates); }
                            if(isGhost){
                                const expanded=expandedExternalFamilies.has(n.id);
                                g.setAttribute('aria-expanded',expanded?'true':'false');
                                const reveal=S('text',{x:p.x+8,y:p.y+H+18,'font-family':'Cinzel,serif','font-size':9,fill:'#5a4532',tabindex:0,role:'button','aria-label':`${expanded?'Masquer':'Afficher'} la famille de ${n.label||'ce conjoint'}`});
                                reveal.textContent=expanded?'Masquer sa famille ▾':'Afficher sa famille ▸';
                                reveal.addEventListener('click',(event)=>{event.stopPropagation(); if(expanded)expandedExternalFamilies.delete(n.id);else expandedExternalFamilies.add(n.id); renderGraph();});
                                reveal.addEventListener('keydown',(event)=>{if(event.key==='Enter'||event.key===' '){event.preventDefault();reveal.dispatchEvent(new MouseEvent('click',{bubbles:true}));}});
                                g.appendChild(reveal);
                            }
                            g.addEventListener('click',()=>{ currentFocusId=n.id; update(n.id); renderGraph(); fitToView(n.id); });
                            g.addEventListener('keydown',(event)=>{ if(event.key==='Enter'||event.key===' '){ event.preventDefault(); g.dispatchEvent(new MouseEvent('click',{bubbles:true})); } });
                            g.addEventListener('dblclick',()=>{ currentFocusId=n.id; update(n.id); renderGraph(); fitToView(n.id); });
                            ng.appendChild(g);
                        });
                        const boxes=[...pos.entries()].filter(([id])=>visible.has(id)).map(([id,p])=>({id,x:p.x,y:p.y}));
                        const overlaps=boxes.some((a,index)=>boxes.slice(index+1).some(b=>a.x<b.x+W&&a.x+W>b.x&&a.y<b.y+H&&a.y+H>b.y));
                        const crossesCard=routedSegments.some(([x1,y1,x2,y2])=>boxes.some(box=>{
                            const minX=Math.min(x1,x2),maxX=Math.max(x1,x2),minY=Math.min(y1,y2),maxY=Math.max(y1,y2);
                            return x1===x2 ? (x1>box.x+1&&x1<box.x+W-1&&maxY>box.y+1&&minY<box.y+H-1) : (y1>box.y+1&&y1<box.y+H-1&&maxX>box.x+1&&minX<box.x+W-1);
                        }));
                        if(overlaps)showErr('Certaines cartes se chevauchent. Réduisez la lecture pour afficher cette branche.');
                        else if(crossesCard)showErr('Un lien traverse une carte. La mise en page doit être ajustée.');
                        else if(err)err.style.display='none';
                    };

                    root.innerHTML=''; root.style.overflow='hidden'; root.style.background='rgba(241,230,204,.28)'; root.style.cursor='grab'; root.appendChild(svg);

                    const zoomInBtn=document.getElementById('tree-zoom-in');
                    const zoomOutBtn=document.getElementById('tree-zoom-out');
                    const zoomResetBtn=document.getElementById('tree-zoom-reset');
                    let scale=1;
                    let tx=40;
                    let ty=40;
                    let panning=false;
                    let panStartX=0, panStartY=0, panStartTx=tx, panStartTy=ty;

                    const clampScale=(v)=>Math.max(0.04, Math.min(2.3, v));
                    const applyView=()=>{ viewport.setAttribute('transform', `translate(${tx} ${ty}) scale(${scale})`); };
                    const getExportBounds=()=>{
                        const values=[...pos.values()];
                        if(!values.length){
                            return { x: 0, y: 0, width: svgW, height: svgH };
                        }
                        const padX=96;
                        const padTop=82;
                        const padBottom=110;
                        const minX=Math.min(...values.map((p)=>p.x)) - padX;
                        const maxX=Math.max(...values.map((p)=>p.x + W)) + padX;
                        const minY=Math.min(...values.map((p)=>p.y)) - padTop;
                        const maxY=Math.max(...values.map((p)=>p.y + H)) + padBottom;
                        return {
                            x: Math.max(0, Math.floor(minX)),
                            y: Math.max(0, Math.floor(minY)),
                            width: Math.ceil(maxX - Math.max(0, minX)),
                            height: Math.ceil(maxY - Math.max(0, minY)),
                        };
                    };
                    const centerOnNode=(nodeId, targetScale)=>{
                        const p=pos.get(Number(nodeId));
                        if(!p) return;
                        if(Number.isFinite(targetScale)) scale=clampScale(targetScale);
                        const cx=p.x+W/2;
                        const cy=p.y+H/2;
                        tx=(root.clientWidth/2)-(cx*scale);
                        ty=(root.clientHeight/2)-(cy*scale);
                        applyView();
                    };
                    const fitToView=(nodeId=currentFocusId)=>{
                        const values=[...pos.values()]; if(!values.length)return;
                        const minX=Math.min(...values.map(p=>p.x)), minY=Math.min(...values.map(p=>p.y));
                        const maxX=Math.max(...values.map(p=>p.x+W)), maxY=Math.max(...values.map(p=>p.y+H));
                        const focus=pos.get(Number(nodeId));
                        const targetX=focus?focus.x+W/2:(minX+maxX)/2, targetY=focus?focus.y+H/2:(minY+maxY)/2;
                        const margin=40, sx=(root.clientWidth-margin*2)/(2*Math.max(targetX-minX,maxX-targetX)), sy=(root.clientHeight-margin*2)/(2*Math.max(targetY-minY,maxY-targetY));
                        scale=clampScale(Math.min(1,sx,sy));
                        tx=root.clientWidth/2-targetX*scale; ty=root.clientHeight/2-targetY*scale; applyView();
                    };
                    const zoomAt=(factor, cx, cy)=>{
                        const ns=clampScale(scale*factor);
                        const wx=(cx-tx)/scale;
                        const wy=(cy-ty)/scale;
                        scale=ns;
                        tx=cx-wx*scale;
                        ty=cy-wy*scale;
                        applyView();
                    };
                    applyView();
                    renderGraph();

                    if(zoomInBtn) zoomInBtn.addEventListener('click', ()=>zoomAt(1.12, root.clientWidth*0.5, root.clientHeight*0.5));
                    if(zoomOutBtn) zoomOutBtn.addEventListener('click', ()=>zoomAt(0.89, root.clientWidth*0.5, root.clientHeight*0.5));
                    if(zoomResetBtn) zoomResetBtn.addEventListener('click', ()=>{
                        fitToView(currentFocusId);
                    });
                    if(branchHighlightToggle) branchHighlightToggle.addEventListener('change', ()=>renderGraph());
                    if(centerFocusBtn) centerFocusBtn.addEventListener('click', ()=>{
                         fitToView(currentFocusId);
                    });

                    root.addEventListener('wheel', (ev)=>{
                        ev.preventDefault();
                        const rect=root.getBoundingClientRect();
                        const cx=ev.clientX-rect.left;
                        const cy=ev.clientY-rect.top;
                        zoomAt(ev.deltaY<0 ? 1.1 : 0.9, cx, cy);
                    }, { passive: false });

                    root.addEventListener('pointerdown', (ev)=>{
                        if(ev.button!==0) return;
                        if(ev.target && ev.target.closest && ev.target.closest('[data-node="1"]')) return;
                        panning=true;
                        root.style.cursor='grabbing';
                        panStartX=ev.clientX; panStartY=ev.clientY; panStartTx=tx; panStartTy=ty;
                        if(root.setPointerCapture) root.setPointerCapture(ev.pointerId);
                    });
                    root.addEventListener('pointermove', (ev)=>{
                        if(!panning) return;
                        tx=panStartTx+(ev.clientX-panStartX);
                        ty=panStartTy+(ev.clientY-panStartY);
                        applyView();
                    });
                    const stopPan=(ev)=>{
                        if(!panning) return;
                        panning=false;
                        root.style.cursor='grab';
                        if(root.releasePointerCapture) root.releasePointerCapture(ev.pointerId);
                    };
                    root.addEventListener('pointerup', stopPan);
                    root.addEventListener('pointercancel', stopPan);

                    if(selected){
                        currentFocusId=selected.id;
                        fitToView(currentFocusId);
                        update(currentFocusId);
                        renderGraph();
                    }
                    window.addEventListener('resize',()=>fitToView(currentFocusId));

                    const exportPngBtn=document.getElementById('tree-export-png');
                    const exportJpgBtn=document.getElementById('tree-export-jpg');
                    const exportPdfBtn=document.getElementById('tree-export-pdf');
                    const exportStatus=document.getElementById('genealogy-export-status');
                    const blobToDataUrl=(blob)=>new Promise((resolve,reject)=>{
                        const reader=new FileReader();
                        reader.onload=()=>resolve(String(reader.result || ''));
                        reader.onerror=()=>reject(new Error('Conversion image impossible.'));
                        reader.readAsDataURL(blob);
                    });
                    const inlineSvgImages=async(clone)=>{
                        const images=[...clone.querySelectorAll('image')];
                        await Promise.all(images.map(async(imageNode)=>{
                            const href=imageNode.getAttribute('href') || imageNode.getAttributeNS('http://www.w3.org/1999/xlink', 'href');
                            if(!href || href.startsWith('data:')) return;
                            try{
                                const response=await fetch(href, { credentials: 'same-origin' });
                                if(!response.ok) throw new Error('Image inaccessible');
                                const blob=await response.blob();
                                const dataUrl=await blobToDataUrl(blob);
                                imageNode.setAttribute('href', dataUrl);
                                imageNode.setAttributeNS('http://www.w3.org/1999/xlink', 'href', dataUrl);
                            } catch(_error){
                                const fallback='data:image/svg+xml,'+encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 160"><rect width="120" height="160" fill="#e1cfad"/><circle cx="60" cy="42" r="27" fill="#9b8060"/><path d="M15 150c2-39 19-61 45-61s43 22 45 61" fill="#9b8060"/></svg>');
                                imageNode.setAttribute('href',fallback);
                                imageNode.setAttributeNS('http://www.w3.org/1999/xlink','href',fallback);
                            }
                        }));
                    };
                    const exportAsPng=async()=>{
                        const originalHighlightState=branchHighlightToggle ? !!branchHighlightToggle.checked : false;
                        if(branchHighlightToggle && originalHighlightState){
                            branchHighlightToggle.checked=false;
                            renderGraph();
                        }

                        try{
                            const bounds=getExportBounds();
                            const clone=svg.cloneNode(true);
                            clone.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                            clone.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
                            clone.setAttribute('width', String(bounds.width));
                            clone.setAttribute('height', String(bounds.height));
                            clone.setAttribute('viewBox', `${bounds.x} ${bounds.y} ${bounds.width} ${bounds.height}`);
                            const cloneViewport=clone.querySelector('g');
                            if(cloneViewport){
                                cloneViewport.setAttribute('transform', 'translate(0 0) scale(1)');
                            }
                            const bgRect=S('rect',{
                                x: bounds.x,
                                y: bounds.y,
                                width: bounds.width,
                                height: bounds.height,
                                fill:'#f1e6cc'
                            });
                            clone.insertBefore(bgRect, clone.firstChild);
                            await inlineSvgImages(clone);

                            const data=new XMLSerializer().serializeToString(clone);
                            const blob=new Blob([data], {type:'image/svg+xml;charset=utf-8'});
                            const url=URL.createObjectURL(blob);

                            try{
                                const png=await new Promise((resolve,reject)=>{
                                    const img=new Image();
                                    img.onload=()=>{
                                        const canvas=document.createElement('canvas');
                                        canvas.width=Math.max(1, Math.round(bounds.width * 2));
                                        canvas.height=Math.max(1, Math.round(bounds.height * 2));
                                        const ctx=canvas.getContext('2d');
                                        if(!ctx){
                                            reject(new Error('Canvas indisponible.'));
                                            return;
                                        }
                                        ctx.fillStyle='#f1e6cc';
                                        ctx.fillRect(0,0,canvas.width,canvas.height);
                                        ctx.drawImage(img,0,0,canvas.width,canvas.height);
                                        resolve(canvas.toDataURL('image/png'));
                                    };
                                    img.onerror=()=>reject(new Error('Impossible d’exporter l’image.'));
                                    img.src=url;
                                });
                                return png;
                            } finally {
                                URL.revokeObjectURL(url);
                            }
                        } finally {
                            if(branchHighlightToggle && originalHighlightState){
                                branchHighlightToggle.checked=true;
                                renderGraph();
                            }
                        }
                    };
                    const exportAsImage=async(format,button)=>{
                        const originalHighlightState=branchHighlightToggle ? !!branchHighlightToggle.checked : false;
                        if(branchHighlightToggle && originalHighlightState){ branchHighlightToggle.checked=false; renderGraph(); }
                        let host=null;
                        try{
                            if(!window.LoreRoomImageExport) throw new Error('La bibliothèque d’export image est indisponible.');
                            const bounds=getExportBounds();
                            const clone=svg.cloneNode(true);
                            clone.setAttribute('xmlns','http://www.w3.org/2000/svg');
                            clone.setAttribute('xmlns:xlink','http://www.w3.org/1999/xlink');
                            clone.setAttribute('width',String(bounds.width));
                            clone.setAttribute('height',String(bounds.height));
                            clone.setAttribute('viewBox',`${bounds.x} ${bounds.y} ${bounds.width} ${bounds.height}`);
                            const cloneViewport=clone.querySelector('g');
                            if(cloneViewport) cloneViewport.setAttribute('transform','translate(0 0) scale(1)');
                            const bgRect=S('rect',{x:bounds.x,y:bounds.y,width:bounds.width,height:bounds.height,fill:'#efe2c9'});
                            clone.insertBefore(bgRect,clone.firstChild);
                            await inlineSvgImages(clone);
                            host=document.createElement('div');
                            host.style.cssText=`position:fixed;left:-100000px;top:0;width:${bounds.width}px;background:#efe2c9;`;
                            clone.style.display='block';
                            host.appendChild(clone);
                            document.body.appendChild(host);
                            const focused=raw.find(n=>Number(n.id)===Number(currentFocusId));
                            const filename=`${window.LoreRoomImageExport.slug(focused ? focused.label : 'arbre')}-${window.LoreRoomImageExport.slug(@json($worldName))}`;
                            await window.LoreRoomImageExport.exportElement({node:host,format,filename,scale:2,button,status:exportStatus});
                        } finally {
                            if(host) host.remove();
                            if(branchHighlightToggle && originalHighlightState){ branchHighlightToggle.checked=true; renderGraph(); }
                        }
                    };
                    if(exportPngBtn) exportPngBtn.addEventListener('click', async()=>{
                        try { await exportAsImage('png',exportPngBtn); }
                        catch(e){ if(exportStatus){exportStatus.hidden=false;exportStatus.textContent=(e&&e.message)?e.message:'L’export PNG a échoué.';} }
                    });
                    if(exportJpgBtn) exportJpgBtn.addEventListener('click', async()=>{
                        try { await exportAsImage('jpg',exportJpgBtn); }
                        catch(e){ if(exportStatus){exportStatus.hidden=false;exportStatus.textContent=(e&&e.message)?e.message:'L’export JPG a échoué.';} }
                    });
                    if(exportPdfBtn){
                        exportPdfBtn.addEventListener('click', async ()=>{
                            try{
                                const png=await exportAsPng();
                                const jspdfNS=window.jspdf || {};
                                const jsPDF=jspdfNS.jsPDF;
                                if(!jsPDF){ throw new Error('Bibliothèque PDF indisponible.'); }
                                const pdf=new jsPDF({orientation:'landscape', unit:'pt', format:'a4'});
                                const pageW=pdf.internal.pageSize.getWidth();
                                const pageH=pdf.internal.pageSize.getHeight();
                                const bounds=getExportBounds();
                                const ratio=Math.min(pageW/(bounds.width*2), pageH/(bounds.height*2));
                                const w=(bounds.width*2)*ratio;
                                const h=(bounds.height*2)*ratio;
                                const x=(pageW-w)/2;
                                const y=(pageH-h)/2;
                                pdf.addImage(png,'PNG',x,y,w,h);
                                pdf.save(`arbre-genealogique-${currentFocusId || 'focus'}.pdf`);
                            } catch(e){ showErr((e && e.message) ? e.message : 'Erreur export PDF.'); }
                        });
                    }

                    window.addEventListener('keydown', (ev)=>{
                        if(!root.contains(document.activeElement) && document.activeElement && ['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName)) return;
                        if(ev.key==='+'){ ev.preventDefault(); zoomAt(1.1, root.clientWidth*0.5, root.clientHeight*0.5); }
                        if(ev.key==='-'){ ev.preventDefault(); zoomAt(0.9, root.clientWidth*0.5, root.clientHeight*0.5); }
                        if(ev.key==='0'){ ev.preventDefault(); if(zoomResetBtn) zoomResetBtn.click(); }
                    });
                } catch(e){ showErr("Erreur JS dans l'arbre généalogique: " + (e&&e.message?e.message:'inconnue')); console.error(e); }
            })();
        </script>
    @endif
@endsection
