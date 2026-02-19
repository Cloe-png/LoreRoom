@extends('manage.layout')

@section('title', 'Gestion - Chroniques')
@section('header', 'Chroniques')

@section('content')
    <style>
        .chrono-select {
            --tone-panel: rgba(255, 252, 245, .72);
            --tone-border: rgba(110, 77, 41, .26);
            --tone-text: #2d2112;
        }
        .select-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }
        .select-title {
            margin: 0;
            color: var(--tone-text);
            letter-spacing: .01em;
        }
        .select-sub {
            margin: 4px 0 0;
            font-size: .92rem;
            color: rgba(56,41,21,.72);
        }
        .select-panel {
            margin-top: 14px;
            padding: 14px;
            border: 1px solid var(--tone-border);
            border-radius: 14px;
            background: linear-gradient(180deg, var(--tone-panel) 0%, rgba(255, 248, 234, .62) 100%);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.55);
        }
        .global-wrap {
            display: flex;
            justify-content: center;
            margin: 4px 0 16px;
        }
        .global-btn {
            min-width: 390px;
            min-height: 66px;
            font-size: 1.15rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            border-width: 2px;
        }
        .characters-tools {
            display: flex;
            justify-content: center;
            margin-bottom: 12px;
        }
        .characters-search {
            width: 100%;
            min-height: 46px;
            border-radius: 12px;
            border: 1px solid var(--tone-border);
            padding: 0 14px;
            background: #fffdf7;
            color: #2c2c2c;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.7), 0 2px 6px rgba(0,0,0,.04);
        }
        .characters-search:focus {
            outline: none;
            border-color: #b78345;
            box-shadow: 0 0 0 3px rgba(183, 131, 69, .18);
        }
        .characters-scroll {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 4px 8px 8px 2px;
            border-radius: 12px;
            background: rgba(255,255,255,.45);
            border: 1px solid rgba(115, 82, 47, .2);
        }
        .mode-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 16px;
            border-radius: 12px;
            border: 1px solid rgba(79, 58, 31, .7);
            background: linear-gradient(180deg, #fffcf4 0%, #efe4ce 100%);
            color: #2b2012;
            font-weight: 700;
            text-decoration: none;
            transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
            box-shadow: 0 2px 0 rgba(70, 50, 25, .25);
        }
        .mode-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37,29,20,.14);
            background: linear-gradient(180deg, #fffef9 0%, #f3e7d2 100%);
        }
        .character-btn {
            width: auto;
            min-height: 44px;
            justify-content: flex-start;
            padding: 0 14px;
            border-left: 6px solid var(--char-color, rgba(79, 58, 31, .7));
            gap: 10px;
            overflow: hidden;
        }
        .character-avatar {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            overflow: hidden;
            flex: 0 0 32px;
            border: 2px solid var(--char-color, rgba(79, 58, 31, .45));
            background: #f8efe0;
            box-shadow: 0 1px 3px rgba(0,0,0,.16);
        }
        .character-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .character-avatar-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .72rem;
            font-weight: 800;
            color: #3a2a17;
            letter-spacing: .02em;
        }
        @media (max-width: 1040px) {
            .characters-scroll {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 640px) {
            .select-head {
                flex-direction: column;
                align-items: flex-start;
            }
            .global-btn {
                width: 100%;
                min-width: 0;
            }
            .select-panel {
                padding: 10px;
            }
            .characters-scroll {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="panel chrono-select">
        <div class="select-head">
            <div>
                <h3 class="select-title">Frise chronologique</h3>
                <p class="select-sub">Choisis une vue: globale ou un personnage.</p>
            </div>
            <a class="btn" href="{{ route('manage.chronicles.create') }}">Nouvelle chronique</a>
        </div>

        <div class="select-panel">
            <div class="global-wrap">
                <a class="mode-btn global-btn" href="{{ route('manage.chronicles.global') }}">Global</a>
            </div>

            <div class="characters-tools">
                <input
                    id="character-filter"
                    class="characters-search"
                    type="search"
                    placeholder="Rechercher un personnage..."
                    autocomplete="off"
                >
            </div>

            <div class="characters-scroll" id="characters-scroll">
                @foreach($characters as $character)
                    @php
                        $charColor = null;
                        if (!empty($character->preferred_color)) {
                            $candidate = trim($character->preferred_color);
                            if ($candidate !== '' && $candidate[0] !== '#') {
                                $candidate = '#' . $candidate;
                            }
                            if (preg_match('/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $candidate)) {
                                $charColor = strtoupper($candidate);
                            }
                        }
                    @endphp
                    <a
                        class="mode-btn character-btn"
                        href="{{ route('manage.chronicles.character', $character) }}"
                        style="{{ $charColor ? '--char-color:' . $charColor . ';' : '' }}"
                        data-character-btn
                        data-character-name="{{ strtolower($character->display_name) }}"
                    >
                        <span class="character-avatar">
                            @if(!empty($character->image_path))
                                <img src="{{ route('media.show', ['path' => $character->image_path]) }}" alt="Photo {{ $character->display_name }}">
                            @else
                                @php
                                    $parts = preg_split('/\s+/', trim($character->display_name));
                                    $a = $parts[0][0] ?? '';
                                    $b = $parts[1][0] ?? '';
                                    $initials = strtoupper($a . $b);
                                @endphp
                                <span class="character-avatar-fallback">{{ $initials !== '' ? $initials : '?' }}</span>
                            @endif
                        </span>
                        <span>{{ $character->display_name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <script>
        (function () {
            var filter = document.getElementById('character-filter');
            var buttons = document.querySelectorAll('[data-character-btn]');

            if (!filter || !buttons.length) {
                return;
            }

            filter.addEventListener('input', function () {
                var value = (filter.value || '').trim().toLowerCase();

                buttons.forEach(function (button) {
                    var name = button.getAttribute('data-character-name') || '';
                    button.style.display = name.indexOf(value) !== -1 ? 'inline-flex' : 'none';
                });
            });
        })();
    </script>
@endsection
