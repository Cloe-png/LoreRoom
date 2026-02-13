<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>LoreRoom - Recit</title>
        <style>
            body {
                margin: 0;
                min-height: 100vh;
                display: grid;
                place-items: center;
                background: #0e0d12;
                color: #eee6d6;
                font-family: "Garamond", "Georgia", serif;
            }

            .panel {
                width: min(720px, 92vw);
                border: 1px solid rgba(199, 164, 106, 0.45);
                background: rgba(24, 20, 28, 0.7);
                padding: 32px;
                border-radius: 10px;
                text-align: center;
            }

            a {
                color: #c7a46a;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <main class="panel">
            <h1>Portail du Recit</h1>
            <p>Page de recit (placeholder).</p>
            <a href="{{ url('/portals') }}">Retour aux portails</a>
        </main>
    </body>
</html>
