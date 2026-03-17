@extends('manage.layout')

@section('title', 'Gestion - Éditer métier')
@section('header', 'Éditer métier')

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.jobs.update', $job) }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label>Monde</label>
                <input type="text" value="{{ optional($defaultWorld)->name ?: 'Monde unique' }}" disabled>
            </div>

            <div class="field">
                <label>Nom</label>
                <input type="text" name="name" value="{{ old('name', $job->name) }}" required>
            </div>

            <div class="field">
                <label>Description</label>
                <textarea name="description">{{ old('description', $job->description) }}</textarea>
            </div>

            <div class="stack">
                <button class="btn" type="submit">Enregistrer</button>
                <a class="btn secondary" href="{{ route('manage.jobs.index') }}">Retour</a>
            </div>
        </form>
    </section>
@endsection
