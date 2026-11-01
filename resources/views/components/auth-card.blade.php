@props(['title', 'subtitle' => __('app.public.auth_subtitle'), 'wide' => false])
<main class="auth-card{{ $wide ? ' auth-card--wide' : '' }}">
    <div class="auth-brand">LOREROOM</div>
    <div class="ornament" aria-hidden="true">✦</div>
    <h1 class="auth-heading">{{ $title }}</h1>
    <div class="ornament" aria-hidden="true">❧</div>
    <p class="auth-subtitle">{{ $subtitle }}</p>
    {{ $slot }}
</main>
