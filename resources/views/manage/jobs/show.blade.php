@extends('manage.layout')

@section('title', 'Gestion - Métier')
@section('header', $job->name)

@section('content')
    <style>
        .job-hero { border:1px solid rgba(101,74,42,.24); border-radius:14px; background:rgba(255,255,255,.55); padding:12px; }
        .job-meta { color:#6b4b2a; font-size:.9rem; }
        .job-list { margin:0; padding-left:18px; }
    </style>

    <div class="stack" style="justify-content: space-between;">
        <div class="job-meta">
            {{ $job->is_default ? 'Par défaut' : 'Personnalisé' }}
            · Monde: {{ optional($job->world)->name ?: 'Global' }}
        </div>
        <div class="stack">
            @if(!$job->is_default)
                <a class="btn secondary" href="{{ route('manage.jobs.edit', $job) }}">Éditer</a>
                <form class="inline" method="POST" action="{{ route('manage.jobs.destroy', $job) }}">
                    @csrf @method('DELETE')
                    <button class="btn danger" type="submit">Supprimer</button>
                </form>
            @endif
        </div>
    </div>

    <section class="panel">
        <div class="job-hero">
            <h2 style="margin:0;">{{ $job->name }}</h2>
            <p class="muted">{{ $job->description ?: 'Aucune description.' }}</p>
        </div>
    </section>

    <section class="panel">
        <h3 style="margin-top:0;">Personnages liés</h3>
        @if($job->characters->isEmpty())
            <p class="muted">Aucun personnage lié.</p>
        @else
            <ul class="job-list">
                @foreach($job->characters as $character)
                    <li><a href="{{ route('manage.characters.show', $character) }}">{{ $character->display_name }}</a></li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection
