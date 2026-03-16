@extends('manage.layout')

@section('title', 'Gestion - Mondes')
@section('header', 'Mondes')

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
    </style>

    @if($worlds->count() === 0)
        <section class="first-world">
            <div>
                <h2>Crée ton premier monde</h2>
                <p>Tu dois avoir au moins un monde pour commencer a ajouter des personnages, lieux et chroniques.</p>
                <a class="plus-btn" href="{{ route('manage.worlds.create') }}" aria-label="Créer un monde">+</a>
            </div>
        </section>
    @else
        <div class="worlds-head">
            <p class="muted" style="margin:0;">Structure principale de chaque univers. Tu peux changer le monde actif ici.</p>
            <a class="btn" href="{{ route('manage.worlds.create') }}">Nouveau monde</a>
        </div>

        <div class="worlds-meta">
            <span class="worlds-chip">Total: {{ $worlds->total() }}</span>
            <span class="worlds-chip">Monde actif: {{ optional($worlds->firstWhere('id', (int) ($activeWorldId ?? 0)))->name ?? 'Aucun' }}</span>
        </div>

        <section class="panel">
            <div class="worlds-grid">
                @foreach($worlds as $world)
                    @php $isActive = (int) $world->id === (int) ($activeWorldId ?? 0); @endphp
                    <article class="world-card @if($isActive) is-active @endif">
                        <div class="world-card-head">
                            <div>
                                <h3 class="world-name">{{ $world->name }}</h3>
                            </div>
                            @if($isActive)
                                <span class="active-badge">Actif</span>
                            @endif
                        </div>

                        <div class="world-card-actions">
                            @if(!$isActive)
                                <form class="inline" method="POST" action="{{ route('manage.worlds.switch', $world) }}">
                                    @csrf
                                    <button class="btn" type="submit">Passer actif</button>
                                </form>
                            @endif

                            <a class="btn secondary" href="{{ route('manage.worlds.show', $world) }}">Voir</a>
                            <a class="btn secondary" href="{{ route('manage.worlds.edit', $world) }}">Éditer</a>

                            <form class="inline" method="POST" action="{{ route('manage.worlds.destroy', $world) }}">
                                @csrf @method('DELETE')
                                <button class="btn danger" type="submit">Supprimer</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top:12px;">{{ $worlds->links() }}</div>
        </section>
    @endif
@endsection
