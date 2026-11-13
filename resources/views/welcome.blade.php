<!DOCTYPE html>
<html data-theme="{{ auth()->check() ? (auth()->user()->theme ?? 'dark') : (in_array(request()->cookie('theme'), ['dark','light','auto'], true) ? request()->cookie('theme') : 'dark') }}" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LoreRoom</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: radial-gradient(circle at 50% 30%, rgba(255, 243, 214, .08), transparent 55%), linear-gradient(180deg, #17131b, #0e0c12);
        }
        .logo {
            width: min(85vw, 720px);
            display: block;
            filter: drop-shadow(0 18px 38px rgba(0, 0, 0, .55));
        }
        .logo img {
            width: 100%;
            height: auto;
            display: block;
        }
    </style>
<link rel="stylesheet" href="{{ asset('css/theme.css') }}">
<meta name="theme-color" content="{{ (auth()->check() ? auth()->user()->theme : request()->cookie('theme')) === 'light' ? '#efe2c9' : '#111723' }}" data-theme-color>
<script>if(document.documentElement.dataset.theme==='auto'){document.documentElement.dataset.theme=matchMedia('(prefers-color-scheme: light)').matches?'light':'dark';var tc=document.querySelector('[data-theme-color]');if(tc)tc.content=document.documentElement.dataset.theme==='light'?'#efe2c9':'#111723';}</script>
</head>
<body>
    <div class="cover-theme-toggle">@include('partials.theme-toggle')</div>
    <a class="logo" href="{{ route('login') }}" aria-label="LoreRoom">
        @include('partials.brand-logo', ['size' => 720])
    </a>
    <script>(function(){var b=document.querySelector('[data-theme-toggle]');var r=document.documentElement;function paint(t){r.dataset.theme=t;b.setAttribute('aria-checked',String(t==='light'));b.querySelector('[data-theme-label]').textContent=t==='light'?'Nuit':'Jour';var m=document.querySelector('[data-theme-color]');if(m)m.content=t==='light'?'#efe2c9':'#111723';}paint(r.dataset.theme);b.addEventListener('click',function(){var t=r.dataset.theme==='light'?'dark':'light';paint(t);document.cookie='theme='+t+'; path=/; max-age=31536000; SameSite=Lax';});})();</script>
</body>
</html>
