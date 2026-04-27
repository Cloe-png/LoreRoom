<!DOCTYPE html>
<html lang="fr">
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
            background: linear-gradient(180deg, #1d2430, #111722);
            color: #f4e7cd;
            font-family: "Garamond", "Georgia", serif;
        }
        .card {
            width: min(680px, 92vw);
            border: 1px solid rgba(228, 191, 127, .35);
            border-radius: 14px;
            padding: 20px;
            background: rgba(255,255,255,.06);
            box-shadow: 0 20px 45px rgba(0,0,0,.35);
        }
        h1 {
            margin: 0 0 8px;
            font-family: "Cinzel", "Times New Roman", serif;
            color: #f6e8cb;
        }
        p { margin: 0 0 16px; color: #dbc9a8; }
        a {
            display: inline-block;
            text-decoration: none;
            border: 1px solid rgba(89, 65, 37, .38);
            color: #2f2418;
            border-radius: 7px;
            padding: 8px 12px;
            background: linear-gradient(180deg, #f9d48f, #e9b461);
            font-family: "Segoe Print", "Comic Sans MS", cursive;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <section class="card">
        <h1>Bienvenue sur LoreRoom</h1>
        <p>Cette page technique existe uniquement comme point d'entrée Laravel.</p>
        <a href="{{ route('login') }}">Aller à la connexion</a>
    </section>
</body>
</html>
