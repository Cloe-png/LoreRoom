@extends('manage.layout')

@section('title', __('app.worlds.page_title'))
@section('header', __('app.worlds.header'))

@section('content')
    <style>
        .worlds-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .worlds-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .worlds-chip {
            border: 1px solid rgba(92, 67, 39, .28);
            background: rgba(255, 255, 255, .28);
            border-radius: 999px;
            padding: 6px 11px;
            font-size: .85rem;
            color: #5b4124;
        }

        .worlds-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 12px;
        }

        .world-card {
            border: 1px solid rgba(106, 77, 42, .32);
            border-radius: 12px;
            background: rgba(255, 255, 255, .24);
            box-shadow: 0 8px 18px rgba(73, 46, 20, .12);
            padding: 12px;
        }

        .world-card.is-active {
            border-color: rgba(48, 119, 90, .45);
            box-shadow: 0 10px 22px rgba(42, 115, 86, .18);
            background: linear-gradient(180deg, rgba(214, 243, 227, .3), rgba(255, 255, 255, .22));
        }

        .world-card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 8px;
        }

        .world-name {
            margin: 0;
            font-family: "Cinzel", "Times New Roman", serif;
            color: #402b16;
            font-size: 1.2rem;
            line-height: 1.1;
        }

        .active-badge {
            border: 1px solid rgba(37, 106, 79, .42);
            border-radius: 999px;
            background: rgba(59, 153, 114, .14);
            color: #255f47;
            padding: 4px 10px;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            white-space: nowrap;
        }

        .world-card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .first-world {
            min-height: 58vh;
            display: grid;
            place-items: center;
            text-align: center;
            border: 1px dashed rgba(102, 74, 43, .38);
            border-radius: 14px;
            background: rgba(255, 255, 255, .2);
            padding: 20px;
        }

        .first-world h2 {
            margin: 0 0 8px;
            color: #493118;
            font-family: "Cinzel", "Times New Roman", serif;
            letter-spacing: .03em;
            font-size: clamp(1.2rem, 2.8vw, 1.8rem);
        }

        .first-world p {
            margin: 0 0 20px;
            color: #6b4f2f;
            max-width: 540px;
        }

        .plus-btn {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 2px solid rgba(89, 65, 37, .42);
            background: linear-gradient(180deg, #f7d18b, #e9b462);
            color: #2f2112;
            font-size: 4rem;
            line-height: 1;
            font-family: "Cinzel", "Times New Roman", serif;
            display: inline-grid;
            place-items: center;
            text-decoration: none;
            box-shadow: 0 14px 26px rgba(73, 46, 20, .24);
        }

        .plus-btn:hover { transform: translateY(-2px); }

        :root { --paper:#f1e6cc; --ink:#2b1d14; --soft:#5a4532; --accent:#6b1e2a; --gold:#a8842f; --leather:#1b1511; }
        .worlds-page { color:var(--ink); }
        .worlds-page * { box-sizing:border-box; }
        .worlds-head { display:grid; grid-template-columns:minmax(0,1fr) auto; align-items:end; gap:20px; margin:0 0 24px; }
        .worlds-heading { min-width:0; }
        .worlds-eyebrow { margin:0 0 5px; color:var(--accent); font:600 11px/1.3 "Cinzel",serif; letter-spacing:.3em; text-transform:uppercase; }
        .worlds-title { margin:0; color:var(--ink); font:700 40px/1.15 "Cinzel",serif; }
        .worlds-title::after { content:"──── ❦ ────"; display:block; margin-top:13px; color:var(--gold); font:400 15px/1 "Cinzel",serif; letter-spacing:.05em; }
        .worlds-description { margin:12px 0 0; color:var(--soft); font:400 20px/1.4 "Cormorant Garamond",serif; }
        .worlds-cta,.world-action { display:inline-flex; align-items:center; justify-content:center; border:1px solid var(--accent); border-radius:2px; background:var(--accent); color:#f6e7c4; box-shadow:2px 3px 0 rgba(40,15,10,.35); font:600 12px/1.2 "Cinzel",serif; letter-spacing:.18em; text-decoration:none; text-transform:uppercase; transition:transform 180ms ease,box-shadow 180ms ease,background 180ms ease; }
        .worlds-cta { padding:12px 20px; white-space:nowrap; }
        .worlds-cta::before { content:"+ "; }
        .world-action { min-height:36px; padding:9px 16px; font-size:11px; }
        .world-action--view { background:var(--accent); color:#f6e7c4; }
        .world-action--edit { background:transparent; color:var(--accent); box-shadow:none; }
        .worlds-page .world-card-actions .btn { min-height:36px; padding:9px 16px; border:1px solid var(--accent); border-radius:2px; background:transparent; color:var(--accent); box-shadow:none; font:600 11px/1.2 "Cinzel",serif; letter-spacing:.18em; text-transform:uppercase; transition:transform 180ms ease,box-shadow 180ms ease,background 180ms ease; }
        .worlds-page .world-card-actions .btn:hover { transform:translate(1px,1px); background:rgba(107,30,42,.06); box-shadow:none; }
        .world-action--delete { margin-left:auto; padding:0 2px; border:0; background:none; box-shadow:none; color:var(--accent); font:italic 17px/1.2 "Cormorant Garamond",serif; letter-spacing:0; text-decoration:underline; text-transform:none; }
        .world-action:hover,.worlds-cta:hover { transform:translate(1px,1px); box-shadow:1px 2px 0 rgba(40,15,10,.25); }
        .world-action--edit:hover { box-shadow:none; background:rgba(107,30,42,.06); }
        .world-action--delete:hover { box-shadow:none; }
        .world-action:focus-visible,.worlds-cta:focus-visible,.world-blank:focus-visible { outline:2px solid var(--accent); outline-offset:3px; }
        .worlds-meta { display:flex; align-items:center; gap:12px; margin:0 0 22px; padding:8px 0; border-top:1px solid #c7ac72; border-bottom:1px solid #c7ac72; color:var(--soft); font:600 11px/1.4 "Cinzel",serif; letter-spacing:.24em; text-transform:uppercase; }
        .worlds-meta strong { color:var(--accent); font-weight:700; }
        .worlds-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:28px; }
        .world-card { position:relative; min-height:190px; padding:28px 30px; border:1px solid #9a7f48; border-radius:3px; outline:1px solid #c7ac72; outline-offset:-5px; background:rgba(255,250,236,.55); box-shadow:3px 4px 0 rgba(70,45,20,.22); }
        .world-card.is-active { background:rgba(255,250,236,.55); border-color:#9a7f48; box-shadow:3px 4px 0 rgba(70,45,20,.22); }
        .world-card-head { margin:0 0 26px; }
        .world-name { color:var(--ink); font:700 26px/1.2 "Cinzel",serif; letter-spacing:.1em; }
        .world-name::after { content:"────── ✦ ──────"; display:block; margin-top:12px; color:var(--gold); font:400 14px/1 "Cinzel",serif; letter-spacing:.04em; }
        .active-badge { position:absolute; top:22px; right:24px; padding:5px 9px; border:2px solid var(--accent); outline:1px solid var(--accent); outline-offset:2px; border-radius:0; background:transparent; color:var(--accent); font:600 11px/1.2 "Cinzel",serif; letter-spacing:.12em; transform:rotate(-6deg); }
        .world-card-actions { align-items:center; gap:10px; margin-top:16px; }
        .world-card-actions > form.inline:not(.world-delete) { margin:0; }
        .world-delete { display:none; }
        .world-blank { min-height:190px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; border:2px dotted #a58b55; border-radius:3px; background:transparent; color:var(--soft); text-decoration:none; transition:transform 180ms ease,border-color 180ms ease; }
        .world-blank:hover { transform:translateY(-2px); border-color:var(--accent); }
        .world-blank-plus { color:var(--accent); font:400 34px/1 "Cormorant Garamond",serif; }
        .world-blank-label { font:600 12px/1.4 "Cinzel",serif; letter-spacing:.16em; text-transform:uppercase; }
        .worlds-page > .worlds-grid { margin-top:0; }
        .worlds-page > .worlds-pagination { margin-top:20px; }
        .worlds-page .first-world { border:2px dotted #a58b55; border-radius:3px; background:transparent; }
        .worlds-page .plus-btn { width:auto; height:auto; padding:12px 20px; border:1px solid var(--accent); border-radius:2px; background:var(--accent); color:#f6e7c4; box-shadow:2px 3px 0 rgba(40,15,10,.35); font:600 12px/1.2 "Cinzel",serif; letter-spacing:.18em; }
        header { background:linear-gradient(110deg,#2a1f18,#1f1712)!important; border-bottom:2px solid var(--gold)!important; }
        header::before,header::after { display:none!important; }
        .title { font-family:"Cinzel",serif!important; }
        .sidebar { background:var(--paper)!important; }
        .sidebar .nav-link,.sidebar .back-portals-link { padding:12px 28px; font-family:"Cinzel",serif; font-size:13px; letter-spacing:.16em; text-transform:uppercase; }
        .sidebar .nav-accordion-body .nav-link { padding:9px 28px 9px 46px; font-size:12px; }
        .sidebar .nav-accordion > summary.nav-link.active { color:var(--soft); font-weight:400; background:transparent; }
        .sidebar .nav-accordion > summary.nav-link.active::before { display:none; }
        .sidebar .nav-accordion > summary.nav-link.active .nav-name { font-weight:400; text-decoration:none; }
        .sidebar .nav-accordion > summary.nav-link { color:var(--soft); }
        .sidebar .nav-accordion > summary.nav-link:hover { color:var(--accent); }
        .sidebar .nav-accordion-body .nav-link.active { color:var(--accent); background:linear-gradient(90deg,rgba(107,30,42,.1),transparent); font-weight:700; }
        .sidebar .nav-accordion-body .nav-link.active::before { width:14px; height:40px; }
        .sidebar-bottom { border-top:1px dotted #a58b55; }
        .sidebar-bottom form { margin-top:5px; border-top:1px dotted rgba(165,139,85,.6); }
        .sidebar-bottom form button.back-portals-link { padding:9px 28px; color:var(--soft); font:italic 16px/1.3 "Cormorant Garamond",serif; letter-spacing:0; text-transform:none; }
        .sidebar :focus-visible { outline:2px solid var(--accent); outline-offset:3px; }
        .worlds-page :focus-visible { outline:2px solid var(--accent); outline-offset:3px; }
        @media(max-width:700px) { .worlds-head { grid-template-columns:1fr; align-items:start; } .worlds-title { font-size:34px; } .worlds-cta { justify-self:start; } .worlds-grid { grid-template-columns:repeat(auto-fill,minmax(min(100%,300px),1fr)); gap:18px; } .worlds-meta { letter-spacing:.12em; } .world-card-actions { flex-wrap:wrap; } }
    </style>

    <div class="worlds-page">
    @if($worlds->count() === 0)
        <section class="first-world">
            <div>
                <h2>{{ __('app.worlds.empty_title') }}</h2>
                <p>{{ __('app.worlds.empty_help') }}</p>
                <a class="plus-btn" href="{{ route('manage.worlds.create') }}" aria-label="{{ __('app.worlds.create_world') }}">+</a>
            </div>
        </section>
    @else
        <div class="worlds-head">
            <div class="worlds-heading">
                <p class="worlds-eyebrow">Gestion</p>
                <h2 class="worlds-title">Mondes</h2>
                <p class="worlds-description">{{ __('app.worlds.overview_help') }}</p>
            </div>
            <a class="worlds-cta" href="{{ route('manage.worlds.create') }}">{{ __('app.worlds.new_world') }}</a>
        </div>

        <div class="worlds-meta">
            <span>{{ $worlds->total() }} monde ❦ Actif : <strong>{{ optional($worlds->firstWhere('id', (int) ($activeWorldId ?? 0)))->name ?? __('app.dom.Aucun') }}</strong></span>
        </div>

            <div class="worlds-grid">
                @foreach($worlds as $world)
                    @php $isActive = (int) $world->id === (int) ($activeWorldId ?? 0); @endphp
                    <article class="world-card @if($isActive) is-active @endif">
                        <div class="world-card-head">
                            <div>
                                <h3 class="world-name">{{ $world->name }}</h3>
                            </div>
                            @if($isActive)
                                <span class="active-badge">{{ __('app.worlds.active') }}</span>
                            @endif
                        </div>

                        <div class="world-card-actions">
                            @if(!$isActive)
                                <form class="inline" method="POST" action="{{ route('manage.worlds.switch', $world) }}">
                                    @csrf
                                    <button class="btn" type="submit">{{ __('app.worlds.activate') }}</button>
                                </form>
                            @endif

                            <a class="world-action world-action--view" href="{{ route('manage.worlds.show', $world) }}">{{ __('app.worlds.view') }}</a>
                            <a class="world-action world-action--edit" href="{{ route('manage.worlds.edit', $world) }}">{{ __('app.worlds.edit') }}</a>

                            <form class="inline world-delete" method="POST" action="{{ route('manage.worlds.destroy', $world) }}">
                                @csrf @method('DELETE')
                            </form>
                            <a class="world-action world-action--delete" href="#" aria-label="{{ __('app.worlds.delete') }} {{ $world->name }}" onclick="event.preventDefault(); this.closest('.world-card-actions').querySelector('.world-delete').requestSubmit();">{{ __('app.worlds.delete') }}</a>
                        </div>
                    </article>
                @endforeach
                <a class="world-blank" href="{{ route('manage.worlds.create') }}" aria-label="{{ __('app.worlds.create_world') }}">
                    <span class="world-blank-plus" aria-hidden="true">+</span>
                    <span class="world-blank-label">Écrire un nouveau monde</span>
                </a>
            </div>

            <div class="worlds-pagination">{{ $worlds->links() }}</div>
    @endif
    </div>
@endsection
