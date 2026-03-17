@extends('manage.layout')

@section('title', 'Gestion - Lore')
@section('header', $entry->title)

@section('content')
    <style>
        .lore-hero {
            border: 1px solid rgba(101,74,42,.24);
            border-radius: 14px;
            background: rgba(255,255,255,.55);
            padding: 12px;
            display: grid;
            gap: 10px;
        }
        .lore-title {
            margin: 0;
            font-family: "Cinzel","Times New Roman",serif;
            color: #4f381b;
        }
        .lore-meta {
            color: #6b4b2a;
            font-size: .9rem;
        }
        .lore-body {
            margin-top: 12px;
            line-height: 1.5;
            color: #2f2418;
            white-space: pre-wrap;
        }
    </style>

    <section class="panel">
        <div class="lore-hero">
            <h2 class="lore-title">{{ $entry->title }}</h2>
            <div class="lore-meta">
                {{ $entry->category ? ucfirst($entry->category) : 'Sans catégorie' }}
                · Monde: {{ optional($entry->world)->name ?: 'Monde actif' }}
            </div>
            @if($entry->tags)
                <div class="muted">Tags: {{ $entry->tags }}</div>
            @endif
        </div>

        <div class="lore-body">
            {{ $entry->content ?: 'Aucun contenu.' }}
        </div>

        <div class="stack" style="margin-top:14px;">
            <a class="btn secondary" href="{{ route('manage.lore.edit', $entry) }}">Éditer</a>
            <form class="inline" method="POST" action="{{ route('manage.lore.destroy', $entry) }}">
                @csrf @method('DELETE')
                <button class="btn danger" type="submit">Supprimer</button>
            </form>
            <a class="btn secondary" href="{{ route('manage.lore.index') }}">Retour</a>
        </div>
    </section>
@endsection
