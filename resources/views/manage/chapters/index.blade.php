@extends('manage.layout')

@section('title', __('app.chapters.page_title'))
@section('header', __('app.chapters.header'))

@section('content')
    <style>
        .chapter-index {
            display: grid;
            gap: 14px;
        }
        .chapter-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            justify-content: space-between;
        }
        .chapter-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
        .chapter-card {
            border: 1px solid rgba(114, 84, 49, .28);
            border-radius: 12px;
            background: linear-gradient(180deg, rgba(255,255,255,.24), rgba(243,228,196,.44));
            padding: 14px;
            box-shadow: 0 8px 18px rgba(70, 45, 19, .10);
        }
        .chapter-card h3 {
            margin: 0 0 6px;
            color: #5d3f20;
            font-family: "Cinzel", "Times New Roman", serif;
            font-size: 1.02rem;
        }
        .chapter-card p {
            margin: 0;
            color: #654c31;
            line-height: 1.45;
        }
        .chapter-date {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            color: #8a6135;
            font-size: .84rem;
            font-weight: 700;
            letter-spacing: .02em;
        }
        .chapter-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }
        .chapter-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 9px;
            border-radius: 999px;
            border: 1px solid rgba(114, 84, 49, .2);
            background: rgba(255,255,255,.52);
            color: #5d4327;
            font-size: .8rem;
        }
        .chapter-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }
    </style>

    <section class="chapter-index">
        <div class="chapter-toolbar">
            <p class="muted" style="margin:0;">{{ __('app.chapters.empty_help') }}</p>
            <a class="btn" href="{{ route('manage.chapters.create') }}">{{ __('app.chapters.new_title') }}</a>
        </div>

        @if($chapters->isEmpty())
            <section class="panel">
                <h2 style="margin-top:0;">{{ __('app.chapters.empty_title') }}</h2>
                <p class="muted">{{ __('app.chapters.no_chapters') }}</p>
            </section>
        @else
            <div class="chapter-grid">
                @foreach($chapters as $chapter)
                    <article class="chapter-card">
                        <h3>{{ $chapter->title }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit((string) ($chapter->summary ?: $chapter->description), 220) }}</p>
                        <div class="chapter-date">{{ __('app.chapters.date') }}: {{ optional($chapter->chapter_date)->format('d/m/Y') ?: __('app.chapters.no_date') }}</div>
                        <div class="chapter-meta">
                            <span class="chapter-chip">{{ $chapter->characters_count }} {{ __('app.chapters.linked_characters') }}</span>
                            <span class="chapter-chip">{{ $chapter->places_count }} {{ __('app.chapters.linked_places') }}</span>
                        </div>
                        <div class="chapter-actions">
                            <a class="btn secondary" href="{{ route('manage.chapters.show', $chapter) }}">{{ __('app.worlds.view') }}</a>
                            <a class="btn secondary" href="{{ route('manage.chapters.edit', $chapter) }}">{{ __('app.chapters.edit') }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
            {{ $chapters->links() }}
        @endif
    </section>
@endsection
