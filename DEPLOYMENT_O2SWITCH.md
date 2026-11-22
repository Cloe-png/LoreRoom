# Déploiement LoreRoom sur o2switch

## Compatibilité

- PHP 8.3 minimum (PHP 8.4 recommandé), avec `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `gd`, `curl`, `dom`, `xml`, `ctype`, `filter`, `hash`, `session`, `tokenizer` et `zip`.
- MySQL ou MariaDB compatible avec les migrations Laravel du projet.
- Le domaine doit servir le contenu de `public/` comme racine web. Le code Laravel, `.env`, `storage/` et `vendor/` restent hors du répertoire publiquement accessible.

## Mise en ligne

Le domaine doit avoir `public/` comme racine web : Apache lance `public/index.php`, puis la route Laravel `/` affiche `resources/views/start.blade.php` comme page d'accueil.

1. Faire une sauvegarde complète de la base et de `storage/app/public`.
2. Choisir PHP 8.3+ dans cPanel et vérifier les extensions listées ci-dessus.
3. Créer une base MySQL et un utilisateur dédiés, avec les droits limités à cette base.
4. Installer les dépendances depuis le projet : `composer install --no-dev --optimize-autoloader`.
5. Créer `.env` à partir de `.env.example`, puis renseigner `APP_ENV=production`, `APP_DEBUG=false`, l’URL HTTPS, les paramètres MySQL et les identifiants mail. Définir `SESSION_SECURE_COOKIE=true` et `SESSION_SAME_SITE=lax`.
6. Générer `APP_KEY` une seule fois avec `php artisan key:generate`. Ne pas la régénérer lors des mises à jour.
7. Construire les ressources localement avec `npm ci && npm run production`, puis transférer le contenu compilé de `public/`.
8. Exécuter `php artisan migrate --force` après sauvegarde. Ne jamais utiliser `migrate:fresh` en production.
9. Vérifier les droits d’écriture de `storage/` et `bootstrap/cache/`. Ne pas créer `public/storage` : les fichiers téléversés sont servis par une route authentifiée.
10. Vider les anciens caches puis reconstruire : `php artisan optimize:clear`, `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`.
11. Vérifier la page d’accueil, la connexion, les images téléversées, les exports PDF et les tâches planifiées. Si le planificateur Laravel est utilisé, configurer une tâche cron chaque minute : `php artisan schedule:run`.

## Réglages de sécurité à garder

- Ne jamais transférer le `.env` local ni le dump SQL ignoré par Git.
- Garder `APP_DEBUG=false`, HTTPS actif et `SESSION_SECURE_COOKIE=true` en production.
- Les routes API demandent maintenant une authentification Sanctum et sont limitées aux données des mondes du compte connecté.
- Le CORS est désactivé par défaut pour les origines externes. Si un frontend séparé est ajouté, définir `CORS_ALLOWED_ORIGINS` avec ses origines HTTPS exactes, séparées par des virgules.
- Utiliser des identifiants de base dédiés, uniques et non administrateurs système. Garder une sauvegarde récente avant chaque migration.

## Note de base de données

Laravel utilise PDO sous le capot avec la connexion MySQL définie par `DB_CONNECTION=mysql`. Les contrôleurs utilisent Eloquent et le Query Builder ; aucun accès direct avec des chaînes SQL construites depuis les entrées utilisateur n’a été relevé dans cette revue.
