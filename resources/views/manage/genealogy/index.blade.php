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
            position: relative;
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
            background: #eef2f6;
        }
        .genealogy-legend-floating .dot.disparu {
            background: #fff3d6;
        }
    </style>
    <section class="panel" style="margin-top:0;">
        <form method="GET" action="{{ route('manage.genealogy.index') }}" class="grid-4">
            <div class="field">
                <label>Personnage</label>
                <select name="focus_id">
                    @foreach($characters as $character)
                        <option value="{{ $character->id }}" {{ $selectedId == $character->id ? 'selected' : '' }}>{{ $character->display_name }}</option>
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
                Arbre descendant classique: parents en haut, enfants et conjoints dessous, puis petits-enfants.
            </p>
            <div class="genealogy-dates-focus">
                <span id="focus-name-chip" class="chip">{{ $focusName }}</span>
                <span id="focus-birth-chip" class="chip birth">Naissance: {{ $focusBirth }}</span>
                <span id="focus-death-chip" class="chip death">Mort: {{ $focusDeath }}</span>
            </div>
            <div class="genealogy-toolbar">
                <button type="button" class="btn secondary" id="tree-export-png">Exporter image</button>
                <button type="button" class="btn secondary" id="tree-export-pdf">Exporter PDF</button>
            </div>
            <div id="genealogy-network-error" class="muted" style="display:none; margin:0 0 10px; padding:10px; border:1px solid rgba(130,60,60,.28); border-radius:8px; background:rgba(255,245,245,.7); color:#6d2a2a;"></div>
            <div class="genealogy-basic-wrap">
                <div class="genealogy-controls">
                    <button type="button" id="tree-zoom-out" title="Zoom -">-</button>
                    <button type="button" id="tree-zoom-reset" title="Recentrer">1:1</button>
                    <button type="button" id="tree-zoom-in" title="Zoom +">+</button>
                </div>
                <div id="genealogy-network" style="height:760px; border:1px solid rgba(114,84,49,.2); border-radius:10px; background:rgba(249,249,249,.92);"></div>
            </div>
        </section>
        <div class="genealogy-legend-floating">
            <span class="item"><span class="line"></span> Enfant</span>
            <span class="item"><span class="line couple"></span> Couple</span>
            <span class="item"><span class="line" style="border-top-style:dotted;"></span> Fratrie</span>
            <span class="item"><span class="dot vivant"></span> Vivant</span>
            <span class="item"><span class="dot mort"></span> Mort</span>
            <span class="item"><span class="dot disparu"></span> Disparu</span>
            <span class="hint">Glisser: déplacer • Molette: zoom</span>
        </div>
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
                    'spouse_id' => (int) ($node['spouse_id'] ?? 0),
                    'status' => (string) ($node['status'] ?? ''),
                    'is_dead' => (($node['status'] ?? '') === 'mort'),
                    'is_selected' => ((int) $node['id'] === (int) $selectedId),
                    'image' => !empty($node['image_path']) ? route('media.show', ['path' => $node['image_path']], false) : null,
                    'level' => (int) ($node['level'] ?? 0),
                ];
            })->values();
        @endphp

        <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
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
                    const focusBirth=document.getElementById('focus-birth-chip');
                    const focusDeath=document.getElementById('focus-death-chip');
                    const min=Math.min(...raw.map(n=>Number(n.level||0)));
                    const nodes=raw.map(n=>({...n,id:Number(n.id),father_id:Number(n.father_id||0),mother_id:Number(n.mother_id||0),spouse_id:Number(n.spouse_id||0),level:Number(n.level||0)-min}));
                    const byId=new Map(nodes.map(n=>[n.id,n])); const ids=new Set(nodes.map(n=>n.id));

                    const cp=[]; const k=new Set(); const addPair=(a,b)=>{ const x=Number(a||0),y=Number(b||0); if(!x||!y||x===y||!ids.has(x)||!ids.has(y))return; const l=Math.min(x,y),r=Math.max(x,y),key=`${l}-${r}`; if(k.has(key))return; k.add(key); cp.push([l,r]); };
                    edges.forEach(e=>{ if((e.kind||'')!=='couple')return; addPair(e.from,e.to); });
                    nodes.forEach(n=>{ if(n.spouse_id&&ids.has(n.spouse_id)) addPair(n.id,n.spouse_id); });
                    nodes.forEach(ch=>{ const f=ch.father_id&&ids.has(ch.father_id)?ch.father_id:0, m=ch.mother_id&&ids.has(ch.mother_id)?ch.mother_id:0; if(f&&m) addPair(f,m); });
                    const partner=new Map(); cp.forEach(([a,b])=>{ if(!partner.has(a))partner.set(a,[]); if(!partner.has(b))partner.set(b,[]); partner.get(a).push(b); partner.get(b).push(a); });

                    const W=200,H=92,DX=68,DXC=34,DY=186,PX=70,PY=40;
                    const levels=[...new Set(nodes.map(n=>n.level))].sort((a,b)=>a-b);
                    const pos=new Map();
                    const unitWidth=(u)=>(u.length===2?(W*2+DXC):W);
                    const buildUnits=(lvl)=>{
                        const row=nodes
                            .filter(n=>n.level===lvl)
                            .sort((a,b)=>String(a.birth_date||'9999').localeCompare(String(b.birth_date||'9999'))||String(a.label||'').localeCompare(String(b.label||'')));
                        const used=new Set(),u=[];
                        row.forEach(n=>{
                            if(used.has(n.id)) return;
                            const p=(partner.get(n.id)||[]).find(x=>!used.has(x)&&byId.get(x)&&byId.get(x).level===lvl);
                            if(p){
                                used.add(n.id); used.add(p);
                                u.push([n.id,p]);
                            } else {
                                used.add(n.id);
                                u.push([n.id]);
                            }
                        });
                        return u;
                    };
                    const widths=levels.map(l=>buildUnits(l).reduce((s,u,i)=>s+unitWidth(u)+(i?DX:0),0));
                    const svgW=Math.max(1200,Math.max(...widths,0)+PX*2), svgH=Math.max(760,PY*2+(Math.max(...levels,0)+1)*DY+H);

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

                    levels.forEach(l=>{
                        const rawUnits=buildUnits(l);
                        const y=PY+l*DY;
                        const metaUnits=rawUnits.map(u=>{
                            const w=unitWidth(u);
                            const prefs=u.map(id=>getNodePreferredCenter(id)).filter(v=>Number.isFinite(v));
                            const pref=prefs.length?prefs.reduce((a,b)=>a+b,0)/prefs.length:NaN;
                            const label=u.map(id=>String(byId.get(id)?.label||'')).join('|');
                            return {u,w,pref,label};
                        });

                        const withPref=metaUnits.filter(x=>Number.isFinite(x.pref)).sort((a,b)=>a.pref-b.pref||a.label.localeCompare(b.label));
                        const withoutPref=metaUnits.filter(x=>!Number.isFinite(x.pref)).sort((a,b)=>a.label.localeCompare(b.label));
                        const ordered=[...withPref,...withoutPref];

                        const hasPref=withPref.length>0;
                        if(!hasPref){
                            const rw=ordered.reduce((s,x,i)=>s+x.w+(i?DX:0),0);
                            let x=(svgW-rw)/2;
                            ordered.forEach((item,idx)=>{
                                const a=item.u;
                                if(a.length===2){ pos.set(a[0],{x,y}); pos.set(a[1],{x:x+W+DXC,y}); x+=W*2+DXC; }
                                else { pos.set(a[0],{x,y}); x+=W; }
                                if(idx<ordered.length-1) x+=DX;
                            });
                            return;
                        }

                        let cursor=PX;
                        ordered.forEach((item)=>{
                            const desired=Number.isFinite(item.pref)?(item.pref-item.w/2):cursor;
                            const left=Math.max(cursor, desired);
                            const a=item.u;
                            if(a.length===2){ pos.set(a[0],{x:left,y}); pos.set(a[1],{x:left+W+DXC,y}); }
                            else { pos.set(a[0],{x:left,y}); }
                            cursor=left+item.w+DX;
                        });
                    });

                    const pairEvidence=new Map();
                    nodes.forEach(ch=>{ const f=ch.father_id&&ids.has(ch.father_id)?ch.father_id:0, m=ch.mother_id&&ids.has(ch.mother_id)?ch.mother_id:0; if(f&&m){ const key=[f,m].sort((a,b)=>a-b).join('-'); pairEvidence.set(key,(pairEvidence.get(key)||0)+1); } });
                    const pickPartner=(p)=>{ const pn=byId.get(p); if(!pn)return 0; const base=(partner.get(p)||[]); const spouse=Number(pn.spouse_id||0); const merged=[...base, ...(spouse? [spouse] : [])]; const candidates=[...new Set(merged)].filter(x=>{ const xn=byId.get(x); return !!xn&&xn.level===pn.level; }); if(!candidates.length)return 0; if(spouse&&candidates.includes(spouse))return spouse; if(candidates.length===1)return candidates[0]; let best=0,bestScore=-1; candidates.forEach(c=>{ const key=[p,c].sort((a,b)=>a-b).join('-'); const score=Number(pairEvidence.get(key)||0); if(score>bestScore){ best=c; bestScore=score; } }); return best||0; };
                    const fam=new Map();
                    nodes.forEach(ch=>{ let ps=[]; const f=ch.father_id&&ids.has(ch.father_id)?ch.father_id:0, m=ch.mother_id&&ids.has(ch.mother_id)?ch.mother_id:0; if(f&&m)ps=[f,m].sort((a,b)=>a-b); else if(f||m){ const p=f||m; const inferred=pickPartner(p); ps=inferred?[p,inferred].sort((a,b)=>a-b):[p]; } else return; const key=ps.join('-'); if(!fam.has(key))fam.set(key,{parents:ps,children:[]}); fam.get(key).children.push(ch.id); });

                    const svg=S('svg',{width:svgW,height:svgH,viewBox:`0 0 ${svgW} ${svgH}`});
                    const viewport=S('g');
                    const eg=S('g'), ng=S('g');
                    viewport.appendChild(eg);
                    viewport.appendChild(ng);
                    svg.appendChild(viewport);
                    const drawLine=(x1,y1,x2,y2,kind)=>{
                        const lineColor=kind==='couple' ? '#3d414a' : '#2a2d33';
                        const lineOpacity=kind==='couple' ? '0.88' : '0.94';
                        const lineWidth=kind==='couple' ? 2.8 : 2.4;
                        const dash=kind==='couple' ? '10 7' : (kind==='sibling' ? '4 6' : '');
                        eg.appendChild(S('line',{
                            x1,y1,x2,y2,
                            stroke:lineColor,
                            'stroke-opacity':lineOpacity,
                            'stroke-width':lineWidth,
                            'stroke-dasharray':dash,
                            'stroke-linecap':'round'
                        }));
                    };

                    const selected=nodes.find(n=>!!n.is_selected) || nodes[0];
                    let currentFocusId=selected ? Number(selected.id) : 0;
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
                    const update=(id)=>{ const n=byId.get(Number(id)); if(!n)return; const l=life(n); if(focusName)focusName.textContent=n.label||'Personnage'; if(focusBirth)focusBirth.textContent=`Naissance: ${l.b}`; if(focusDeath)focusDeath.textContent=`Mort: ${l.d}`; };
                    const statusFill=(status)=>{
                        const s=String(status||'').toLowerCase();
                        if(s==='mort') return '#eef2f6';
                        if(s==='disparu') return '#fff3d6';
                        return '#ffffff';
                    };
                    const renderGraph=()=>{
                        eg.innerHTML='';
                        ng.innerHTML='';
                        const visible=new Set(nodes.map(n => n.id));

                        cp.forEach(([a,b])=>{
                            if(!visible.has(a) || !visible.has(b)) return;
                            const A=pos.get(a),B=pos.get(b); if(!A||!B)return;
                            const L=A.x<=B.x?A:B,R=A.x<=B.x?B:A;
                            drawLine(L.x+W,L.y+H/2,R.x,R.y+H/2,'couple');
                        });

                        fam.forEach(f=>{
                            const parents=(f.parents || []).filter(id=>visible.has(id));
                            const children=(f.children || []).filter(id=>visible.has(id));
                            if(!parents.length || !children.length) return;
                            const pp=parents.map(id=>pos.get(id)).filter(Boolean);
                            if(!pp.length) return;
                            const ch=children.map(id=>pos.get(id)).filter(Boolean).sort((a,b)=>a.x-b.x);
                            if(!ch.length) return;

                            const py=Math.max(...pp.map(p=>p.y+H));
                            const pc=pp.map(p=>p.x+W/2).sort((a,b)=>a-b);
                            const by=py+44;
                            let ax=pc[0];

                            if(pc.length>=2){
                                const left=pc[0], right=pc[pc.length-1];
                                const bridgeY=py+20;
                                pp.forEach(p=>drawLine(p.x+W/2, py, p.x+W/2, bridgeY, 'lineage'));
                                drawLine(left, bridgeY, right, bridgeY, 'lineage');
                                ax=(left+right)/2;
                                drawLine(ax, bridgeY, ax, by, 'lineage');
                            } else {
                                ax=pc[0];
                                drawLine(ax, py, ax, by, 'lineage');
                            }

                            const fx=ch[0].x+W/2,lx=ch[ch.length-1].x+W/2;
                            const barLeft=Math.min(fx,lx,ax);
                            const barRight=Math.max(fx,lx,ax);
                            drawLine(barLeft,by,barRight,by,'lineage');
                            ch.forEach(p=>drawLine(p.x+W/2,by,p.x+W/2,p.y,'lineage'));
                        });

                        siblingPairs.forEach(([a,b])=>{
                            if(!visible.has(a) || !visible.has(b)) return;
                            const A=pos.get(a), B=pos.get(b); if(!A||!B) return;
                            drawLine(A.x+W/2, A.y+H+8, B.x+W/2, B.y+H+8, 'sibling');
                        });

                        nodes.forEach(n=>{
                            if(!visible.has(n.id)) return;
                            const p=pos.get(n.id); if(!p)return;
                            const isFocused=n.id===currentFocusId;
                            const g=S('g',{style:'cursor:pointer', 'data-node':'1'});
                            g.appendChild(S('rect',{x:p.x,y:p.y,width:W,height:H,rx:14,fill:statusFill(n.status),stroke:isFocused?'#2f5fa8':'#2e3036','stroke-width':isFocused?3:2}));
                            const parts=toNameParts(String(n.label||'Personnage'));
                            const titleEl=S('title');
                            titleEl.textContent=`${parts.first || ''} ${parts.last || ''}`.trim();
                            g.appendChild(titleEl);
                            const cx=p.x+40, cy=p.y+(H/2), r=27;
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
                            g.appendChild(S('circle',{cx,cy,r:r+1.5,fill:'#ffffff',stroke:isFocused?'#2f5fa8':'#7f8a99','stroke-width':isFocused?3:2}));
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
                                g.appendChild(S('circle',{cx,cy,r,fill:n.is_dead?'#d8e0ea':'#e8edf5'}));
                                const init=S('text',{x:cx,y:cy+5,'text-anchor':'middle','font-family':'Georgia, serif','font-size':'17',fill:'#3a4453','font-weight':'700'});
                                init.textContent=initials(parts);
                                g.appendChild(init);
                            }
                            const fullName=shortLabel(`${parts.first}${parts.last ? ` ${parts.last}` : ''}`.trim(), 16);
                            const nameText=S('text',{
                                x:p.x+78,
                                y:p.y+52,
                                'font-family':'Georgia, serif',
                                'font-size':'13',
                                fill:'#17181b',
                                'font-weight':'700',
                                'clip-path':`url(#${textClipId})`
                            });
                            nameText.textContent=fullName;
                            g.appendChild(nameText);
                            g.addEventListener('click',()=>{ currentFocusId=n.id; update(n.id); renderGraph(); centerOnNode(n.id, Math.max(scale, 1)); });
                            g.addEventListener('dblclick',()=>{ currentFocusId=n.id; update(n.id); renderGraph(); centerOnNode(n.id, Math.max(scale, 1.25)); });
                            ng.appendChild(g);
                        });
                    };

                    root.innerHTML=''; root.style.overflow='hidden'; root.style.background='linear-gradient(180deg,#f8f8f8,#f1f1f1)'; root.style.cursor='grab'; root.appendChild(svg);

                    const zoomInBtn=document.getElementById('tree-zoom-in');
                    const zoomOutBtn=document.getElementById('tree-zoom-out');
                    const zoomResetBtn=document.getElementById('tree-zoom-reset');
                    let scale=1;
                    let tx=Math.max(24,(root.clientWidth-svgW)/2);
                    let ty=24;
                    let panning=false;
                    let panStartX=0, panStartY=0, panStartTx=tx, panStartTy=ty;

                    const clampScale=(v)=>Math.max(0.45, Math.min(2.3, v));
                    const applyView=()=>{ viewport.setAttribute('transform', `translate(${tx} ${ty}) scale(${scale})`); };
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
                        scale=1;
                        tx=Math.max(24,(root.clientWidth-svgW)/2);
                        ty=24;
                        applyView();
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
                        centerOnNode(currentFocusId, 1);
                        update(currentFocusId);
                        renderGraph();
                    }

                    const exportPngBtn=document.getElementById('tree-export-png');
                    const exportPdfBtn=document.getElementById('tree-export-pdf');
                    const exportAsPng=()=>new Promise((resolve,reject)=>{
                        try{
                            const clone=svg.cloneNode(true);
                            const bg=S('rect',{x:0,y:0,width:svgW,height:svgH,fill:'#f8f8f8'});
                            clone.insertBefore(bg, clone.firstChild);
                            const data=new XMLSerializer().serializeToString(clone);
                            const blob=new Blob([data], {type:'image/svg+xml;charset=utf-8'});
                            const url=URL.createObjectURL(blob);
                            const img=new Image();
                            img.onload=()=>{
                                const canvas=document.createElement('canvas');
                                canvas.width=Math.round(svgW*2);
                                canvas.height=Math.round(svgH*2);
                                const ctx=canvas.getContext('2d');
                                ctx.fillStyle='#f8f8f8';
                                ctx.fillRect(0,0,canvas.width,canvas.height);
                                ctx.drawImage(img,0,0,canvas.width,canvas.height);
                                URL.revokeObjectURL(url);
                                resolve(canvas.toDataURL('image/png'));
                            };
                            img.onerror=()=>{ URL.revokeObjectURL(url); reject(new Error('Export image impossible.')); };
                            img.src=url;
                        } catch(e){ reject(e); }
                    });
                    if(exportPngBtn){
                        exportPngBtn.addEventListener('click', async ()=>{
                            try{
                                const png=await exportAsPng();
                                const a=document.createElement('a');
                                a.href=png;
                                a.download=`arbre-genealogique-${currentFocusId || 'focus'}.png`;
                                document.body.appendChild(a);
                                a.click();
                                a.remove();
                            } catch(e){ showErr((e && e.message) ? e.message : 'Erreur export image.'); }
                        });
                    }
                    if(exportPdfBtn){
                        exportPdfBtn.addEventListener('click', async ()=>{
                            try{
                                const png=await exportAsPng();
                                const jspdfNS=window.jspdf || {};
                                const jsPDF=jspdfNS.jsPDF;
                                if(!jsPDF){ throw new Error('Bibliotheque PDF indisponible.'); }
                                const pdf=new jsPDF({orientation:'landscape', unit:'pt', format:'a4'});
                                const pageW=pdf.internal.pageSize.getWidth();
                                const pageH=pdf.internal.pageSize.getHeight();
                                const ratio=Math.min(pageW/(svgW*2), pageH/(svgH*2));
                                const w=(svgW*2)*ratio;
                                const h=(svgH*2)*ratio;
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
                } catch(e){ showErr("Erreur JS dans l'arbre genealogique: " + (e&&e.message?e.message:'inconnue')); console.error(e); }
            })();
        </script>
    @endif
@endsection

