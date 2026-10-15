@extends('manage.layout')

@section('title', __('app.worlds.world_title'))
@section('header', __('app.worlds.edit'))

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.worlds.update', $world) }}">
            @csrf @method('PUT')
            <div class="field">
                <label>{{ __('app.worlds.name') }}</label>
                <input type="text" name="name" value="{{ old('name', $world->name) }}" required>
            </div>
            <div class="field">
                <label>{{ __('app.worlds.description') }}</label>
                <textarea name="summary" rows="6" placeholder="{{ __('app.worlds.summary_placeholder') }}">{{ old('summary', $world->summary) }}</textarea>
            </div>
            <div class="stack">
                <button class="btn" type="submit">{{ __('app.worlds.save') }}</button>
                <a class="btn secondary" href="{{ route('manage.worlds.index') }}">{{ __('app.worlds.back') }}</a>
            </div>
        </form>
    </section>
@endsection
