@props(['title', 'subtitle' => __('app.public.auth_subtitle'), 'wide' => false, 'mascot' => false, 'mascotImage' => null])
@if($mascot)<div class="auth-card-mascot-wrap">@endif
<main class="auth-card{{ $wide ? ' auth-card--wide' : '' }}">
    @include('partials.brand-logo', ['size' => 80, 'class' => 'auth-brand'])
    <div class="ornament" aria-hidden="true">✦</div>
    <h1 class="auth-heading">{{ $title }}</h1>
    <div class="ornament" aria-hidden="true">❧</div>
    <p class="auth-subtitle">{{ $subtitle }}</p>
    {{ $slot }}
</main>
@if($mascot)
    <x-auth-mascot :size="450" :image="$mascotImage" />
</div>
@endif
