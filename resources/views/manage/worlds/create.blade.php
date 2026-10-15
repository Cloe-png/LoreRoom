@extends('manage.layout')

@section('title', __('app.worlds.page_title'))
@section('header', __('app.worlds.new_world'))

@section('content')
    <section class="panel">
        <form method="POST" action="{{ route('manage.worlds.store') }}">
            @csrf
            <div class="field">
                <label>{{ __('app.worlds.name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="field">
                <label>{{ __('app.worlds.description') }}</label>
                <textarea name="summary" rows="6" placeholder="{{ __('app.worlds.summary_placeholder') }}">{{ old('summary') }}</textarea>
            </div>
            <div class="stack">
                <button class="btn" type="submit">{{ __('app.worlds.create') }}</button>
                <a class="btn secondary" href="{{ route('manage.worlds.index') }}">{{ __('app.worlds.cancel') }}</a>
            </div>
        </form>
    </section>
@endsection
