@extends('manage.layout')

@section('title', 'Gestion - Relation')
@section('header', 'Detail relation')

@section('content')
    <section class="panel">
        <div class="grid-4">
            <div class="card" style="grid-column: span 2;">
                <strong>Personnage source</strong>
                <p>{{ optional($relation->fromCharacter)->display_name ?: '-' }}</p>
            </div>
            <div class="card" style="grid-column: span 2;">
                <strong>Personnage cible</strong>
                <p>{{ optional($relation->toCharacter)->display_name ?: '-' }}</p>
            </div>
        </div>
        <div class="grid-4">
            <div class="card">
                <strong>Type</strong>
                <p>{{ $relation->display_type ?? $relation->relation_type }}</p>
            </div>
            <div class="card">
                <strong>Intensite</strong>
                <p>{{ $relation->intensity ?: '-' }}/10</p>
            </div>
            <div class="card">
                <strong>Sens</strong>
                <p>{{ $relation->is_bidirectional ? 'Bidirectionnelle' : 'Unidirectionnelle' }}</p>
            </div>
            <div class="card">
                <strong>Maj</strong>
                <p>{{ optional($relation->updated_at)->format('Y-m-d H:i') }}</p>
            </div>
        </div>
        <div class="card">
            <strong>Description</strong>
            <p style="white-space: pre-line;">{{ $relation->description ?: 'Aucune description.' }}</p>
        </div>
        <div class="stack" style="margin-top:12px;">
            <a class="btn secondary" href="{{ route('manage.relations.edit', $relation) }}">Éditer</a>
            <a class="btn secondary" href="{{ route('manage.relations.index') }}">Retour</a>
        </div>
    </section>
@endsection
