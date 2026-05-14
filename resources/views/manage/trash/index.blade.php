@extends('manage.layout')

@section('title', 'Gestion - Corbeille')
@section('header', 'Corbeille')

@section('content')
    <style>
        .trash-shell { display: grid; gap: 14px; }
        .trash-hero {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(114, 84, 49, .34);
            border-radius: 14px;
            padding: 20px 18px;
            background:
                radial-gradient(560px 200px at 8% -10%, rgba(255, 234, 196, .82), transparent 62%),
                linear-gradient(180deg, rgba(248, 237, 215, .97), rgba(228, 208, 176, .95));
            box-shadow: 0 16px 34px rgba(67, 45, 20, .16);
        }
        .trash-title {
            margin: 0;
            color: #4a3117;
            font-family: "Cinzel", "Times New Roman", serif;
            letter-spacing: .04em;
            font-size: clamp(1.25rem, 2vw, 1.8rem);
        }
        .trash-text {
            margin: 8px 0 0;
            color: #6b5031;
            max-width: 760px;
            line-height: 1.5;
        }
        .trash-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .trash-panel {
            padding: 0;
            overflow: hidden;
            border-radius: 14px;
        }
        .trash-panel-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 14px 16px 12px;
            border-bottom: 1px solid rgba(114, 84, 49, .18);
            background: linear-gradient(180deg, rgba(255,255,255,.24), rgba(255,255,255,.1));
        }
        .trash-panel-title {
            margin: 0;
            color: #56391b;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1rem;
            letter-spacing: .04em;
        }
        .trash-panel-meta {
            color: #75583a;
            font-size: .86rem;
        }
        .trash-list {
            display: grid;
            gap: 10px;
            padding: 14px 16px 16px;
        }
        .trash-item {
            display: grid;
            gap: 8px;
            border: 1px solid rgba(101,74,42,.22);
            border-radius: 12px;
            background: rgba(255,255,255,.34);
            padding: 12px;
        }
        .trash-item-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .trash-name {
            color: #342415;
            font-size: 1rem;
            font-weight: 700;
        }
        .trash-meta {
            color: #5f4730;
            font-size: .9rem;
            line-height: 1.4;
        }
        .trash-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .trash-empty {
            padding: 18px 16px 20px;
        }
        @media (max-width: 980px) {
            .trash-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="trash-shell">
        <section class="trash-hero">
            <h2 class="trash-title">Corbeille de l'application</h2>
            <p class="trash-text">
                Retrouve ici les éléments supprimés et restaure-les quand tu veux. Tu peux aussi supprimer definitivement un élément, vider toute la corbeille, et les contenus encore présents depuis plus de 30 jours seront nettoyés automatiquement.
            </p>
            <form method="POST" action="{{ route('manage.trash.empty') }}" onsubmit="return confirm('Vider toute la corbeille ? Cette action est definitive.');" style="margin-top:14px;">
                @csrf
                <button class="btn danger" type="submit">Vider la corbeille</button>
            </form>
        </section>

        <section class="trash-grid">
            @foreach($sections as $section)
                <section class="panel trash-panel">
                    <div class="trash-panel-top">
                        <h3 class="trash-panel-title">{{ $section['title'] }}</h3>
                        <div class="trash-panel-meta">{{ $section['items']->count() }} élément(s)</div>
                    </div>

                    @if($section['items']->isEmpty())
                        <div class="trash-empty">
                            <p class="muted" style="margin:0;">Aucun élément dans cette section.</p>
                        </div>
                    @else
                        <div class="trash-list">
                            @foreach($section['items'] as $item)
                                <article class="trash-item">
                                    <div class="trash-item-top">
                                        <div>
                                            <div class="trash-name">{{ data_get($item, $section['name']) ?: 'Sans nom' }}</div>
                                            <div class="trash-meta">
                                                Supprimé le {{ optional($item->deleted_at)->format('d/m/Y H:i') ?: '-' }}<br>
                                                Purge auto le {{ optional($item->deleted_at)->copy()->addDays(30)->format('d/m/Y') ?: '-' }}
                                            </div>
                                        </div>
                                        <div class="trash-actions">
                                            <form method="POST" action="{{ route('manage.trash.restore', ['type' => $section['type'], 'id' => $item->id]) }}">
                                                @csrf
                                                <button class="btn secondary" type="submit">Restaurer</button>
                                            </form>
                                            <form method="POST" action="{{ route('manage.trash.destroy', ['type' => $section['type'], 'id' => $item->id]) }}" onsubmit="return confirm('Supprimer definitivement cet element ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn danger" type="submit">Supprimer</button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach
        </section>
    </div>
@endsection