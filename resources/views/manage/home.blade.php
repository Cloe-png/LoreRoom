@extends('manage.layout')
@section('title', 'Carnet de bord')
@section('content')
<h1>Bonjour {{ pathinfo(auth()->user()->name, PATHINFO_FILENAME) }}</h1>
<p class="hi">Voici où en est le monde de {{ $activeWorld->name ?? 'votre carnet' }}.</p>
<div class="fl" aria-hidden="true"><i></i><span>❦</span><i></i></div>

@if($activeWorld && $todayBirthdays->isNotEmpty())
    @include('components.birthday-banner', ['birthdays' => $todayBirthdays, 'birthdayToday' => $today])
@endif

@if($activeWorld)
    <section class="card hero">
        <div>
            <div class="lab">Monde actif</div>
            <h2>{{ mb_strtoupper($activeWorld->name) }}</h2>
            <div class="fl" style="margin:12px 0 14px" aria-hidden="true"><i></i><span>✦</span><i></i></div>
            @if(trim((string) $activeWorld->summary) !== '')
                <p>{{ $activeWorld->summary }}</p>
                <a class="lk" href="{{ route('manage.worlds.edit', $activeWorld) }}">Modifier la description</a>
            @endif
        </div>
        <div class="div">
            <div class="lab">Sommaire du monde</div>
            <div class="ldg">
                @foreach($worldCounts as $item)
                    <a class="li {{ $item['count'] ? '' : 'z' }}" href="{{ route($item['route']) }}"><span>{{ $item['label'] }}</span><i class="dts"></i><b>{{ $item['count'] ?: '—' }}</b></a>
                @endforeach
            </div>
        </div>
    </section>

    <div class="cols">
        <div class="col">
            @if($steps)
                <section class="card">
                    <span class="prog">{{ collect($steps)->where('done', true)->count() }} / {{ count($steps) }}</span>
                    <h3>Premiers pas</h3><p class="sub">Elles se cochent toutes seules.</p>
                    @foreach($steps as $step)
                        <div class="qs {{ $step['done'] ? 'd' : '' }}"><span class="bx">{{ $step['done'] ? '✓' : '' }}</span><span class="t">{{ $step['label'] }}</span>@unless($step['done'])<a class="btn" href="{{ $step['url'] }}">Commencer</a>@endunless</div>
                    @endforeach
                </section>
            @endif

            @if($incompleteItems->isEmpty())
                <section class="card"><h3>À compléter</h3><div class="ok"><i>✓</i><span>Toutes tes fiches sont complètes.</span></div></section>
            @else
                <section class="card"><h3>À compléter</h3><p class="sub">Les fiches commencées, à reprendre.</p>
                    @foreach($incompleteItems as $item)
                        <div class="row"><b><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></b><span class="ty">{{ $item['type'] }}</span><span class="mi">Modifié {{ $item['updated']->locale('fr')->diffForHumans() }} · il manque : {{ $item['missing'] }}</span></div>
                    @endforeach
                    @if($incompleteItems->isNotEmpty())<a class="lk" href="{{ route('manage.index', ['incomplete' => 1]) }}">Voir tout</a>@endif
                </section>
            @endif

            <section class="card"><h3>Chronologie</h3>
                @if($timeline->isEmpty())
                    <p class="sub">La frise attend son premier événement.</p><div class="tl g"><div class="ev g"><div class="dt">Année ?</div><div class="tt">Le tout premier événement…</div></div><div class="ev g"><div class="dt">Année ?</div><div class="tt">Puis ce qui en découle…</div></div></div>
                    <a class="btn" href="{{ route('manage.chronicles.create') }}">Ajouter un événement</a>
                @else
                    <p class="sub">Prochain événement, puis les plus récents.</p><div class="tl">
                    @foreach($timeline as $event)
                        <div class="ev {{ $loop->first && $event->event_date && $event->event_date->isFuture() ? 'n' : '' }}"><div class="dt">@if($loop->first && $event->event_date && $event->event_date->isFuture())<span class="tag">À VENIR</span>@endif{{ $event->event_date ? $event->event_date->format('d/m/Y') : 'Date inconnue' }}</div><div class="tt"><a href="{{ route('manage.chronicles.show', $event) }}">{{ $event->title }}</a></div><div class="ty">@if($event->eventPlace){{ $event->eventPlace->name }}@endif @foreach($event->linkedCharacters as $character) · {{ $character->display_name }} @endforeach</div></div>
                    @endforeach</div><a class="lk" href="{{ route('manage.chronicles.global') }}">Ouvrir la frise</a>
                @endif
            </section>
        </div>
        <div class="col">
            @if($idea)
                <section class="idea"><div class="lab">IDÉE DU JOUR</div><p>{{ $idea['text'] }}</p><div class="bt"><a class="btn p" href="{{ $idea['url'] }}">{{ $idea['createLabel'] ?? 'Écrire la réponse' }}</a>@if(!empty($idea['another']))<form method="POST" action="{{ route('manage.prompts.another') }}">@csrf<button class="lk" type="submit">Une autre idée</button></form>@endif</div></section>
            @endif
            @if(auth()->user()->canAccessModule('suggestions'))<div class="sg"><a class="lk" href="{{ route('manage.suggestions.index') }}">Une idée pour Loreroom ?</a></div>@endif
            @if($latestNews)<section class="card">@if($newsIsNew)<span class="stamp">NOUVEAU</span>@endif<h3>Nouveautés</h3><div class="dt">{{ \Carbon\Carbon::parse($latestNews->published_at)->locale('fr')->translatedFormat('j F Y') }}</div><div style="font-family:'Cinzel',serif;font-size:14px;margin:4px 0;color:var(--heading)">{{ $latestNews->title }}</div><p class="sub" style="margin:0">{{ \Illuminate\Support\Str::limit(strip_tags($latestNews->body), 140) }}</p></section>@endif
        </div>
    </div>
@else
    <div class="cols">
        <div class="col">
            @if($steps)
                <section class="card">
                    <h3>Premiers pas</h3><p class="sub">Crée un monde pour commencer ton univers.</p>
                    @foreach($steps as $step)
                        <div class="qs {{ $step['done'] ? 'd' : '' }}"><span class="bx">{{ $step['done'] ? '✓' : '' }}</span><span class="t">{{ $step['label'] }}</span>@unless($step['done'])<a class="btn" href="{{ $step['url'] }}">Commencer</a>@endunless</div>
                    @endforeach
                </section>
            @endif
            @if($latestNews)
                <section class="card">@if($newsIsNew)<span class="stamp">NOUVEAU</span>@endif<h3>Nouveautés</h3><div class="dt">{{ \Carbon\Carbon::parse($latestNews->published_at)->locale('fr')->translatedFormat('j F Y') }}</div><div style="font-family:'Cinzel',serif;font-size:14px;margin:4px 0;color:var(--heading)">{{ $latestNews->title }}</div><p class="sub" style="margin:0">{{ \Illuminate\Support\Str::limit(strip_tags($latestNews->body), 140) }}</p></section>
            @endif
        </div>
        <div class="col">
            @if(auth()->user()->canAccessModule('suggestions'))<div class="sg"><a class="lk" href="{{ route('manage.suggestions.index') }}">Une idée pour Loreroom ?</a></div>@endif
        </div>
    </div>
@endif
@endsection
