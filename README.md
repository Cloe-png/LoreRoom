# LoreRoom

LoreRoom est une application de gestion de mondes et de personnages destinée à organiser des univers, des relations et des chronologies, avec une interface pensée pour la narration et les fiches de création.

**Fonctionnalités principales**
- Gestion multi-mondes avec sélection du monde actif
- Fiches personnages complètes (identité, apparence, objectifs, secrets, pouvoirs, etc.)
- Relations entre personnages (famille, social, couples, ex, fratries)
- Arbre généalogique interactif avec export PNG/PDF
- Chroniques et timelines globales ou par personnage
- Lieux, factions, métiers, espèces et lore
- Galerie d’images liée aux personnages

**Technos**
- Laravel (backend)
- Blade + JS (frontend)
- MySQL (persistance)
- Laravel Mix (assets)

**Démarrage rapide**
1. Installer les dépendances PHP
   - `composer install`
1. Installer les dépendances front
   - `npm install`
1. Préparer la configuration
   - Copier `.env.example` vers `.env`
   - Renseigner `DB_*`, `APP_URL`, etc.
1. Générer la clé d’app
   - `php artisan key:generate`
1. Lancer les migrations
   - `php artisan migrate`
1. Lier le stockage public
   - `php artisan storage:link`
1. Compiler les assets
   - `npm run dev`
1. Démarrer le serveur
   - `php artisan serve`

**Accès**
- Connexion: `/login`
- Gestion: `/manage`

**Sessions**
La session expire après 15 minutes d’inactivité (config via `SESSION_LIFETIME`). Un middleware force la déconnexion et redirige vers la connexion en cas d’inactivité.

**Scripts front**
- `npm run dev` pour le développement
- `npm run watch` pour le watch
- `npm run prod` pour la build de prod

**Structure des modules**
- Mondes: `manage/worlds`
- Personnages: `manage/characters`
- Arbre généalogique: `manage/arbre-genealogique`
- Relations: `manage/relations`
- Chroniques: `manage/chronicles`
- Lieux: `manage/places`
- Factions: `manage/factions`
- Métiers: `manage/jobs`
- Espèces: `manage/species`
- Lore: `manage/lore`
- Galerie: `manage/galerie`
