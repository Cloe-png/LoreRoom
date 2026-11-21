<!DOCTYPE html>
<html data-theme="{{ auth()->check() ? (auth()->user()->theme ?? 'dark') : (in_array(request()->cookie('theme'), ['dark','light','auto'], true) ? request()->cookie('theme') : 'dark') }}" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/brand/loreroom.png') }}?v=1">
    <link rel="apple-touch-icon" href="{{ asset('images/brand/loreroom.png') }}?v=1">
    <title>@yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap" rel="stylesheet">
    <style>
        :root{--paper:#f1e6cc;--ink:#2b1d14;--soft:#5a4532;--accent:#6b1e2a;--gold:#a8842f;--leather:#1b1511;--line:#9a7f48;--inner:#c7ac72}
        *{box-sizing:border-box}
        body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px 16px;color:var(--ink);font-family:'Cormorant Garamond',serif;background:var(--leather)}
        .auth-bg{position:fixed;inset:0;z-index:-1;background-image:radial-gradient(ellipse at center,transparent 25%,rgba(0,0,0,.6) 100%),linear-gradient(rgba(27,21,17,.55),rgba(27,21,17,.55)),url('{{ asset('images/backgrounds/museum_background.jpg') }}');background-position:center;background-size:cover;filter:blur(4px) saturate(.8);transform:scale(1.03)}
        .auth-card{position:relative;width:min(440px,100%);padding:40px;background:repeating-linear-gradient(0deg,rgba(90,60,30,.035) 0 1px,transparent 1px 3px),var(--paper);border:1px solid var(--line);border-radius:3px;box-shadow:4px 6px 0 rgba(0,0,0,.35)}
        .auth-card:before{content:"";position:absolute;inset:5px;border:1px solid var(--inner);pointer-events:none}
        .auth-brand,.auth-heading{text-align:center;font-family:Cinzel,serif;font-weight:700}
        .auth-brand{font-size:24px;letter-spacing:.32em;padding-left:.32em}
        .auth-heading{font-size:28px;line-height:1.2;margin:19px 0 8px}
        .ornament{display:flex;align-items:center;justify-content:center;gap:12px;color:var(--gold);font-size:15px;margin:7px auto 12px}
        .ornament:before,.ornament:after{content:"";width:52px;height:1px;background:var(--gold)}
        .auth-subtitle{text-align:center;margin:0 0 22px;color:var(--soft);font-size:18px;font-style:italic}
        .auth-footer{border-top:1px solid rgba(168,132,47,.45);margin-top:24px;padding-top:12px;text-align:center;font-size:15px;font-style:italic;color:var(--soft)}
        .auth-message{padding:9px 12px;margin:0 0 16px;background:#ebdcb8;border-left:3px solid var(--gold);border-radius:2px;font-size:17px;font-style:italic}
        .auth-message:before{content:"❧ ";color:var(--gold)}.auth-message.error{border-left-color:var(--accent)}.auth-message.error:before{color:var(--accent)}
        .field{margin:0 0 16px}.field label,.role-section legend{display:block;margin:0 0 6px;font:700 11px Cinzel,serif;letter-spacing:.18em;text-transform:uppercase;color:var(--soft)}
        input:not([type=checkbox]),select{display:block;width:100%;height:46px;padding:8px 11px;border:1px solid var(--line);border-radius:2px;background:rgba(255,250,236,.85);color:var(--ink);font:18px 'Cormorant Garamond',serif}
        input::placeholder{font-style:italic;color:#76624d}input:focus-visible,select:focus-visible,button:focus-visible,a:focus-visible{outline:2px solid var(--accent);outline-offset:2px}
        input:-webkit-autofill{-webkit-box-shadow:0 0 0 1000px #fbf4df inset;-webkit-text-fill-color:#2b1d14;caret-color:#2b1d14;transition:background-color 9999s}
        .field input[aria-invalid=true]{border-color:var(--accent)}.field-error{margin:4px 0 0;color:var(--accent);font-size:16px;font-style:italic}
        .password-wrap{position:relative}.password-wrap input{padding-right:86px}.toggle-password{position:absolute;right:8px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:var(--accent);font:700 10px Cinzel,serif;cursor:pointer}
        .forgot-link{display:block;text-align:right;margin:-8px 0 16px;font-size:16px;font-style:italic;color:var(--accent)}
        .remember{display:flex;align-items:center;gap:9px;margin:12px 0 18px;font-size:17px;cursor:pointer}.remember input,.role-card input{appearance:none;flex:0 0 18px;width:18px;height:18px;margin:0;border:1px solid var(--line);border-radius:1px;background:#fffaf0;cursor:pointer}.remember input:checked:after,.role-card input:checked:after{content:'✓';display:block;text-align:center;color:var(--accent);font:bold 15px/16px serif}
        .actions{display:grid;gap:10px}.btn{display:flex;align-items:center;justify-content:center;width:100%;min-height:46px;padding:10px 14px;border:1px solid var(--accent);border-radius:2px;background:var(--accent);color:#f6e7c4;text-decoration:none;text-align:center;font:700 12px Cinzel,serif;letter-spacing:.18em;text-transform:uppercase;cursor:pointer;box-shadow:2px 3px 0 rgba(40,15,10,.35);transition:transform 180ms,box-shadow 180ms}.btn:hover{transform:translate(1px,1px);box-shadow:1px 2px 0 rgba(40,15,10,.35)}.btn:disabled{opacity:.7;cursor:wait}
        .btn.secondary{background:transparent;color:var(--accent)}.or{display:flex;align-items:center;gap:12px;color:var(--soft);font-size:16px;font-style:italic}.or:before,.or:after{content:"";height:1px;flex:1;background:var(--inner)}
        .help,.footer-help{color:var(--soft);font-size:16px;font-style:italic;line-height:1.35}.role-section{border:0;padding:0;margin:8px 0 18px}.role-section legend{padding:0}.role-grid{display:grid;gap:8px}.role-card{display:grid;grid-template-columns:18px 1fr;gap:0 9px;padding:9px;border:1px solid rgba(154,127,72,.6);border-radius:2px;cursor:pointer}.role-card input{grid-row:span 2}.role-card strong{font:700 12px Cinzel,serif;color:var(--ink)}.role-card span{font-size:15px;color:var(--soft)}.grid{display:grid;gap:12px}.footer-help{margin-top:18px;padding-top:12px;border-top:1px solid rgba(168,132,47,.45)}
        @media(min-width:481px){.auth-card{width:min(440px,100%)}}
        @media(max-width:480px){.auth-card{padding:24px;margin:0 0}.auth-brand{font-size:21px}.auth-heading{font-size:26px}}
        @media(prefers-reduced-motion:reduce){*,*:before,*:after{scroll-behavior:auto!important;transition:none!important}}
    </style>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <link rel="stylesheet" href="{{ mix('css/auth-mascot.css') }}">
<link rel="stylesheet" href="{{ asset('css/theme.css') }}">
<meta name="theme-color" content="{{ (auth()->check() ? auth()->user()->theme : request()->cookie('theme')) === 'light' ? '#efe2c9' : '#111723' }}" data-theme-color>
<script>if(document.documentElement.dataset.theme==='auto'){document.documentElement.dataset.theme=matchMedia('(prefers-color-scheme: light)').matches?'light':'dark';var tc=document.querySelector('[data-theme-color]');if(tc)tc.content=document.documentElement.dataset.theme==='light'?'#efe2c9':'#111723';}</script>
</head>
<body class="auth-page"><div class="auth-theme-toggle">@include('partials.theme-toggle')</div>
    <div class="auth-bg" aria-hidden="true"></div>
    @yield('content')
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach(function(button){button.addEventListener('click',function(){var input=document.getElementById(button.getAttribute('aria-controls'));var shown=input.type==='password';input.type=shown?'text':'password';button.setAttribute('aria-pressed',String(shown));button.textContent=shown?@json(__('app.public.hide_password')):@json(__('app.public.show_password'));});});
        document.querySelectorAll('form[data-auth-form]').forEach(function(form){form.addEventListener('submit',function(){var button=form.querySelector('[data-submit-button]');if(button){button.disabled=true;button.textContent=button.dataset.loadingText;}});});
        var firstInvalid=document.querySelector('.auth-card [aria-invalid="true"]');if(firstInvalid){firstInvalid.focus();}
    </script>
<script>(function(){var b=document.querySelector('[data-theme-toggle]');if(!b)return;var root=document.documentElement;function paint(t){root.dataset.theme=t;b.setAttribute('aria-checked',String(t==='light'));b.querySelector('[data-theme-label]').textContent=t==='light'?'Nuit':'Jour';var m=document.querySelector('[data-theme-color]');if(m)m.content=t==='light'?'#efe2c9':'#111723';}paint(root.dataset.theme);b.addEventListener('click',function(){var t=root.dataset.theme==='light'?'dark':'light';paint(t);document.cookie='theme='+t+'; path=/; max-age=31536000; SameSite=Lax';});})();</script>
<script src="{{ mix('js/auth-mascot.js') }}" defer></script></body>
</html>
