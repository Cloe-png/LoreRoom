@extends('manage.layout')

@section('title', __('app.chapters.page_title'))
@section('header', __('app.chapters.header'))

@section('content')
    <style>
        .chapter-show {
            display: grid;
            gap: 14px;
            max-width: 980px;
        }
        .chapter-hero {
            border: 1px solid rgba(114, 84, 49, .32);
            border-radius: 12px;
            padding: 16px 18px;
            background:
                radial-gradient(560px 200px at 8% -10%, rgba(255, 239, 208, .86), transparent 62%),
                linear-gradient(180deg, rgba(249, 238, 216, .98), rgba(236, 220, 189, .97));
            box-shadow: 0 8px 18px rgba(70, 45, 19, .12);
        }
        .chapter-hero h2 {
            margin: 0 0 8px;
            color: #6a4724;
            font-family: "Cinzel", "Times New Roman", serif;
        }
        .chapter-hero-date {
            margin: 0;
            color: #8a6135;
            font-weight: 700;
        }
        .chapter-block {
            border: 1px solid rgba(114, 84, 49, .22);
            border-radius: 12px;
            background: rgba(255,255,255,.18);
            padding: 14px;
        }
        .chapter-block h3 {
            margin: 0 0 10px;
            color: #6a4724;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1rem;
        }
        .chapter-list {
            display: grid;
            gap: 8px;
        }
        .chapter-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(108,77,43,.18);
            background: rgba(255,255,255,.32);
        }
        .chapter-row strong {
            color: #2f2013;
        }
        .chapter-row .chapter-role {
            display: block;
            margin-top: 4px;
            color: #8a6135;
            font-size: .84rem;
            font-weight: 700;
        }
        .chapter-outfit-image {
            width: 100%;
            max-width: 220px;
            display: block;
            margin-top: 8px;
            border-radius: 10px;
            border: 1px solid rgba(108,77,43,.16);
            box-shadow: 0 6px 14px rgba(70, 45, 19, .08);
        }
        .chapter-row span {
            color: #6b5031;
        }
    </style>

    <div class="chapter-show">
        <section class="chapter-hero">
            <h2>{{ $chapter->title }}</h2>
            <p class="chapter-hero-date">{{ __('app.chapters.date') }}: {{ optional($chapter->chapter_date)->format('d/m/Y') ?: __('app.chapters.no_date') }}</p>
            @if($chapter->summary)
                <p class="chapter-summary" style="margin:0 0 10px;color:#6b5031;line-height:1.5;">{{ $chapter->summary }}</p>
            @endif
            <p class="account-text" style="margin:0;">{{ $chapter->description ?: __('app.chapters.empty_help') }}</p>
            <div class="stack" style="margin-top:14px;">
                <a class="btn secondary" href="{{ route('manage.chapters.edit', $chapter) }}">{{ __('app.chapters.edit') }}</a>
                <form method="POST" action="{{ route('manage.chapters.destroy', $chapter) }}" onsubmit="return confirm('Supprimer ce chapitre ?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn danger" type="submit">{{ __('app.chapters.delete') }}</button>
                </form>
                <a class="btn secondary" href="{{ route('manage.chapters.index') }}">{{ __('app.chapters.back') }}</a>
            </div>
        </section>

        <section class="chapter-block">
            <h3>{{ __('app.chapters.linked_characters') }}</h3>
            @if($chapter->characters->isEmpty())
                <p class="muted">{{ __('app.chapters.no_characters') }}</p>
            @else
                <div class="chapter-list">
                    @foreach($chapter->characters as $character)
                        <div class="chapter-row">
                            <strong>{{ $character->display_name }}</strong>
                            <span>{{ $character->preferred_color ?: 'â€”' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="chapter-block">
            <h3>{{ __('app.chapters.linked_places') }}</h3>
            @if($chapter->places->isEmpty())
                <p class="muted">{{ __('app.chapters.no_places') }}</p>
            @else
                <div class="chapter-list">
                    @foreach($chapter->places as $place)
                        <div class="chapter-row">
                            <strong>{{ $place->name }}</strong>
                            <span>{{ $place->region ?: 'â€”' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="chapter-block">
            <h3>{{ __('app.chapters.auto_feelings') }}</h3>
            @if(empty($chapter->character_feelings))
                <p class="muted">{{ __('app.chapters.no_characters') }}</p>
            @else
                <div class="chapter-list">
                    @foreach($chapter->character_feelings as $feeling)
                        @php
                            $matchedCharacter = $chapter->characters->firstWhere('id', (int) ($feeling['character_id'] ?? 0));
                            $fallbackRole = $matchedCharacter ? $matchedCharacter->role : null;
                            $characterRole = $feeling['character_role'] ?? $fallbackRole;
                        @endphp
                        <div class="chapter-row">
                            <div>
                                <strong>{{ $feeling['character_name'] ?? __('app.chapters.feelings_title') }}</strong>
                                @if(!empty($characterRole))
                                    <span class="chapter-role">{{ __('app.chapters.character_role') }}: {{ $characterRole }}</span>
                                @endif
                                @if(!empty($feeling['outfit_image_path']))
                                    <img class="chapter-outfit-image" src="{{ route('media.show', ['path' => $feeling['outfit_image_path']]) }}" alt="{{ $feeling['outfit'] ?: __('app.chapters.character_outfit') }}">
                                @endif
                                @if(!empty($feeling['outfit']))
                                    <span class="chapter-role">{{ __('app.chapters.character_outfit') }}: {{ $feeling['outfit'] }}</span>
                                @endif
                                @if(!empty($feeling['emotion']))
                                    <span class="chapter-role">{{ __('app.chapters.character_emotion') }}: {{ $feeling['emotion'] }}</span>
                                @endif
                                @if(!empty($feeling['note']))
                                    <div class="muted" style="margin-top:4px;">{{ $feeling['note'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
