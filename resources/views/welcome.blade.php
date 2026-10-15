<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
</head>
<body>
    <a class="logo" href="{{ route('login') }}" aria-label="LoreRoom">
        @include('partials.brand-logo', ['size' => 720])
    </a>
</body>
</html>
