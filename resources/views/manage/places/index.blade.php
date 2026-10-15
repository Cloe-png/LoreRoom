@extends('manage.layout')
@section('title', 'Gestion - Lieux')
@section('header', 'Lieux')
@section('content')
<style>
.place-page{color:var(--ink,#2b1d14);font-family:'Cormorant Garamond',serif}.place-head{display:flex;justify-content:space-between;align-items:end;gap:20px;margin-bottom:22px}.place-label,.place-page label{font-family:Cinzel,serif;font-size:11px;letter-spacing:.24em;text-transform:uppercase;color:var(--soft,#5a4532)}.place-title{font:700 36px Cinzel,serif;margin:3px 0}.place-rule{color:var(--gold,#a8842f);letter-spacing:.2em}.place-filters,.place-row,.place-card,.place-form details{background:rgba(255,250,236,.55);border:1px solid #9a7f48;outline:1px solid #c7ac72;outline-offset:-5px;border-radius:3px;box-shadow:3px 4px 0 rgba(70,45,20,.22)}.place-filters{padding:16px;display:flex;gap:12px;align-items:end;flex-wrap:wrap;margin-bottom:20px}.place-page input,.place-page select,.place-page textarea{background:var(--paper,#f1e6cc);border:1px solid #9a7f48;border-radius:2px;color:var(--ink,#2b1d14);font:18px 'Cormorant Garamond',serif;padding:8px 10px;min-height:42px}.place-page .btn,.place-page button.btn{border-radius:2px;font:600 11px Cinzel,serif;letter-spacing:.18em;text-transform:uppercase;padding:10px 16px;border:1px solid var(--accent,#6b1e2a);background:var(--accent,#6b1e2a);color:#f6e7c4;box-shadow:2px 3px 0 rgba(40,15,10,.35)}.place-page .secondary{background:transparent;color:var(--accent,#6b1e2a);box-shadow:none}.place-page .btn:hover{transform:translate(1px,1px);box-shadow:1px 1px 0 rgba(40,15,10,.25);transition:180ms}.place-list{display:grid;gap:14px}.place-row{padding:17px 20px;display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:14px}.place-row a{color:var(--ink,#2b1d14);text-decoration:none}.place-row a:hover{color:var(--accent,#6b1e2a)}.place-row-title{font:700 20px Cinzel,serif}.place-meta{font:11px Cinzel,serif;letter-spacing:.16em;text-transform:uppercase;color:var(--soft,#5a4532)}.place-stamp{display:inline-block;border:2px solid var(--accent,#6b1e2a);outline:1px solid #a8842f;outline-offset:2px;color:var(--accent,#6b1e2a);font:11px Cinzel,serif;padding:4px 7px;transform:rotate(-5deg)}.place-empty{text-align:center;padding:42px;font:italic 25px Caveat,cursive}.place-page a:focus-visible,.place-page button:focus-visible,.place-page input:focus-visible,.place-page select:focus-visible{outline:2px solid #6b1e2a;outline-offset:3px}@media(max-width:820px){.place-head{align-items:start;flex-direction:column}.place-row{grid-template-columns:1fr}}
</style>
<main class="place-page">
 <header class="place-head"><div><div class="place-label">Gestion</div><h1 class="place-title">Lieux</h1><div class="place-rule">──────── ❦ ────────</div><p>Cartographie des territoires et des lieux de votre monde.</p></div><a class="btn" href="{{ route('manage.places.create') }}">+ Nouveau lieu</a></header>
 <form class="place-filters" method="GET" action="{{ route('manage.places.index') }}">
  <div><label for="place-q">Recherche</label><br><input id="place-q" name="q" value="{{ request('q') }}" placeholder="Nom ou description"></div>
  <div><label for="place-type">Type</label><br><select id="place-type" name="type"><option value="">Tous</option>@foreach($types as $type)<option value="{{ $type }}" @selected(request('type')===$type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
  <div><label for="place-status">Statut</label><br><select id="place-status" name="status"><option value="">Tous</option>@foreach($statuses as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ str_replace('_',' ',ucfirst($status)) }}</option>@endforeach</select></div>
  <button class="btn secondary" type="submit">Filtrer</button>
  <a class="btn secondary" href="{{ route('manage.places.index') }}">Effacer</a>
  <a class="btn secondary" href="{{ route('manage.places.index',array_merge(request()->query(),['view'=>request('view')==='flat'?'tree':'flat'])) }}">{{ request('view')==='flat' ? 'Vue arborescente' : 'Vue à plat' }}</a>
 </form>
 @if($places->isEmpty())
  <section class="place-card place-empty"><p>Les cartes sont encore vierges…</p><a class="btn" href="{{ route('manage.places.create') }}">+ Nouveau lieu</a></section>
 @else
  <div class="place-list" id="places-tree">@foreach($tree as $row)@php($place=$row['place'])
   <article class="place-row" style="margin-left:{{ request('view')==='flat' ? 0 : min($row['depth']*26,130) }}px">
    <div><a class="place-row-title" href="{{ route('manage.places.show',$place) }}">{{ $place->name }}</a><div class="place-meta">{{ ucfirst($place->type) }}@if($place->parent) · {{ $place->parent->name }}@endif</div><p>{{ $place->short_description ?: $place->summary }}</p></div>
    <span class="place-stamp">{{ str_replace('_',' ',strtoupper($place->status ?: 'inconnu')) }}</span>
   </article>
  @endforeach</div>
 @endif
</main>
@endsection
