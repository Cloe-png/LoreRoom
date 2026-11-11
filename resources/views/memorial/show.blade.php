{{-- TODO(design): hommage --}}
@extends('manage.layout')
@section('head')<link rel="stylesheet" href="{{ asset('css/memorial.css') }}">@endsection
@section('title', __('memorial.title', [], 'fr'))
@section('header', __('memorial.title', [], 'fr'))
@section('content')
<div class="memorial-page">
    @if(!empty($offerMessage))<p role="status" aria-live="polite">{{ $offerMessage }}</p>@endif
    {{-- tombe --}}
    <section class="memorial-tomb" aria-labelledby="memorial-title">
        <h2 id="memorial-title">{{ __('memorial.title', [], 'fr') }}</h2>
        <p>{{ __('memorial.subtitle', [], 'fr') }}</p>
        <p>{{ __('memorial.birth', [], 'fr') }}</p>
        <p>{{ __('memorial.epitaph', [], 'fr') }}</p>
        @if($visitCount >= 3)<p>{{ __('memorial.rumor', [], 'fr') }}</p>@endif
        <a class="btn secondary" href="{{ route('manage.index') }}">{{ __('memorial.back', [], 'fr') }}</a>
    </section>
    {{-- offrande --}}
    <section class="memorial-offering" aria-label="Offrande">
        <x-memorial-flower />
        <form method="POST" action="{{ route('manage.memorial.offer') }}">@csrf
            <button class="btn" type="submit" @if($offeredToday) disabled @endif>{{ $offeredToday ? __('memorial.offered', [], 'fr') : __('memorial.offer', [], 'fr') }}</button>
        </form>
    </section>
    {{-- vue chef --}}
    @if($isChef)
    <section class="memorial-chef" aria-labelledby="memorial-chef-title">
        <h2 id="memorial-chef-title">Vue chef</h2><p>Visible uniquement par le rôle chef.</p>
        <div id="memorial-stats" aria-live="polite">
            <p>Personnes distinctes : {{ $stats['people'] }}</p>
            <p>Fleurs au total : {{ $stats['total'] }} · Aujourd’hui : {{ $stats['today'] }}</p>
            <ul>@foreach($stats['last_seven_days'] as $day)<li>{{ $day['date'] }} : {{ $day['count'] }}</li>@endforeach</ul>
        </div>
    </section>
    @endif
</div>
@endsection
