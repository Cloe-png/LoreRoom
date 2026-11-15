# Rapport de ménage LoreRoom

## Préparation et mesure initiale

- Le tag `avant-menage` pointe sur `b991364`; la branche de travail est `menage`.
- Le dépôt était propre avant le démarrage de cette tâche.
- `php artisan test` avant l’ajout du SmokeTest : **40 tests, 33 réussis, 7 échoués**. Les échecs étaient dans `MemorialAndChefTest` (six cas) et `TourTest` (un cas). Les symptômes visibles incluaient une expiration de session quand le test fait avancer l’horloge, un décompte de fleurs supérieur à l’attendu et l’absence de cibles du tutoriel chef/admin. Cette mesure précède le SmokeTest.
- `php artisan route:list` : code retour 0, **163 routes** (645 lignes de sortie détaillée avec les middlewares).
- Premier `npm run dev` : échec sur un littéral `\\n` final dans `resources/css/museum.css`, présent dans le commit précédent. Réparé sur `menage` par `3ff65d3`; une nouvelle compilation a réussi. Mix signale encore que les données Browserslist sont anciennes.
- `tests/Feature/SmokeTest.php` a été ajouté après cette mesure et passe seul (1 test réussi). Il vérifie 22 pages pour un utilisateur chef et un monde synthétiques.

## Inventaire fait avant suppression

| Chemin / élément | Catégorie | Certitude | Preuve et décision |
| --- | --- | --- | --- |
| `app/Http/Controllers/ManageController.php` | Contrôleur ancien | CERTAIN | Aucune route ne le cible; sa seule référence externe est un `use` inutilisé dans `routes/web.php`. La route d’accueil cible `DashboardHomeController`, qui rend `manage.home`. Supprimé dans `e89edcf` avec l’ancien écran qu’il rend. |
| `resources/views/manage/index.blade.php` | Vue ancienne | CERTAIN | Référencée uniquement par le contrôleur sans route ci-dessus; l’accueil réel rend `resources/views/manage/home.blade.php`. Supprimé dans `e89edcf`. |
| `resources/views/manage.blade.php` | Vue ancienne | CERTAIN | Aucun `view('manage')`, `View::make`, include ni route ne la référence. Supprimé dans `e89edcf`. |
| `resources/views/welcome.blade.php` | Vue Laravel par défaut | CERTAIN | `/` rend `start`; aucun appel à `welcome` ni include dynamique trouvé. Supprimé dans `e89edcf`. |
| `resources/js/app.js`, `resources/js/bootstrap.js`, `public/js/app.js` | Bundle JS scaffold | CERTAIN | Aucun template ne charge `js/app.js`; supprimés dans `7440df9`, avec l’entrée Mix et celle du manifeste. |
| Dépendances npm directes `axios`, `lodash` | Dépendances | CERTAIN | Axios, uniquement importé par le bootstrap JS non chargé, a été retiré dans `7440df9`. Lodash reste dans le lockfile comme dépendance transitive de Mix. `html-to-image`, Mix et PostCSS ont des références actives et restent. |
| `composer.phar` | Outil local versionné | CERTAIN | Binaire Composer à la racine, aucun appel applicatif. Retiré de l’index dans `3d9b3fa`; le fichier local reste. |
| `.composer/cache/files/**/*.zip` | Cache Composer versionné | CERTAIN | 112 archives de cache Composer suivies par Git, non nécessaires à l’application. Retirées de l’index dans `3d9b3fa`; le cache local reste. |
| `loreroom (3).sql` | Dump local versionné | CERTAIN | Dump contenant des enregistrements et informations d’identification hachées. Retiré de l’index dans `3d9b3fa`; le fichier local reste. Le contenu existe toujours dans l’historique Git. |
| `routes/web.php` — import `ManageController` | Import mort | CERTAIN | Le nom n’est utilisé que par sa déclaration d’import et la classe elle-même, qui n’est pas enregistrée comme action de route. Suppression prévue avec le contrôleur. |
| `public/css/dashboard-reference.css` | Feuille CSS | CERTAIN | Aucun template, manifeste ni fichier source ne la charge; supprimée lors de l’audit complémentaire du 15 novembre 2026. |
| `public/help.jpg`, `write1.png`, `write2.png`, `cadre.png` à la racine | Images hors répertoire applicatif | À VÉRIFIER | Aucun appel applicatif direct trouvé pour ces copies racine. Elles peuvent être des fichiers source/souvenirs utilisateur; conservées. Le cadre utilisé est `public/images/museum/cadre.png`; le `cadre.png` racine a servi de source à sa conversion. |
| `mcamara/laravel-localization` et `config/laravellocalization.php` | Dépendance/config de langue | CERTAIN | Aucun appel, middleware, alias ou groupe de routes du paquet n’est utilisé; le français est fixé par le middleware maison. Retirés lors de l’audit complémentaire du 15 novembre 2026. |
| `resources/views/partials/i18n.blade.php` | Partial Blade vide | CERTAIN | Fichier vide (un saut de ligne), inclus par trois layouts; inclusions et partial retirés lors de l’audit complémentaire du 15 novembre 2026. |
| `users.preferred_locale`, `users.email_verified_at`, `password_resets`, `failed_jobs` | Schéma/framework | À VÉRIFIER | Le code applicatif les utilise peu ou indirectement; les mécanismes Laravel et les données réelles peuvent en dépendre. Aucune table ni colonne ne sera supprimée ou modifiée. `preferred_locale` reste explicitement conservée. |

### Éléments vérifiés et conservés

- Les routes actuelles exposent **163 entrées**. Les ressources API, routes alias (`/portals`, `/register`), routes de compatibilité, ainsi que les pages protégées par la demande (thèmes, chef, tutoriel, mascotte, hommage, idées du jour, nouveautés, musée et vue grille) sont conservées.
- Les vues Blade actives ont des appels directs ou sont incluses/composées. Les vues `manage.home`, auth, memorial, changelog et modules restent.
- `PlaceRequest` est la classe mère de `StorePlaceRequest` et `UpdatePlaceRequest`. Les commandes prune sont découvertes par `app/Console/Kernel.php`; `MakeUserChef` est explicitement à conserver. Policies, middlewares, modèles relationnels et composants Laravel peuvent être résolus dynamiquement : aucun n’est retiré sur une simple recherche textuelle.
- Les images publiques `museum_background.jpg`, `loreroom.png`, les quatre illustrations de la page d’accueil et `public/images/mascot/help.jpg` ont des références. Les ressources récentes protégées restent en place.
- Aucun `dd()`, `dump()`, `var_dump()` ou `console.log()` applicatif n’a été trouvé; les correspondances du scan large étaient des faux positifs (`copy()`, `.add()`, texte CSS/HTML).
- Aucun marqueur courant de clé AWS/Google/GitHub/OpenAI ou clé privée PEM n’a été trouvé dans les sources scannées. Une adresse Gmail réelle apparaît dans `resources/lang/fr/app.php` et une autre dans le dump SQL; elles ne sont pas recopiées ici. Le dump SQL contient aussi un hash bcrypt. Ces éléments restent lisibles dans l’historique.
- Aucun fichier `.bak` ou `.orig` applicatif n’a été identifié. Aucune migration ne sera supprimée ou modifiée. Aucun sélecteur CSS d’une feuille toujours chargée ne sera retiré.
- Aucune dépendance Composer n’est marquée CERTAIN inutilisée à ce stade. Les dépendances Laravel/CORS, Sanctum, DomPDF et outils de développement ont des usages ou des effets indirects; elles restent.

## Tables et colonnes candidates — inventaire seulement

À examiner ultérieurement avec la base de production et les intégrations :

- `users.preferred_locale` (ignorée par le réglage français, mais conservée explicitement).
- `users.email_verified_at` (champ Laravel standard sans flux de vérification évident dans les routes actuelles).
- `password_resets` (aucune route de réinitialisation dédiée dans l’application; le broker Laravel peut néanmoins l’utiliser).
- `failed_jobs` (table Laravel standard, malgré la configuration de file synchrone actuelle).

Ce sont des candidates de revue, pas des suppressions proposées. Aucun schéma n’est touché.

## Suppressions réalisées

- `e89edcf` : retrait du contrôleur `ManageController`, des trois vues d’accueil orphelines et de son import mort. 5 fichiers touchés, 3 079 lignes supprimées.
- `3d9b3fa` : retrait de l’index Git de `.composer` (112 archives et 2 fichiers `.htaccess`), `composer.phar` et `loreroom (3).sql`, soit 116 fichiers; tous restent sur le poste. `.gitignore` ignore désormais `.composer/`, `/composer.phar` et `*.sql`.
- `7440df9` : suppression du scaffold JS `app.js`/`bootstrap.js`, du bundle généré et de la dépendance Axios directe; retrait de l’entrée Mix et de l’entrée manifeste. Lodash est conservé car Mix en dépend encore transitivement.
- Audit complémentaire du 15 novembre 2026 : `mcamara/laravel-localization` et ses dépendances exclusives, son fichier de configuration, le partial i18n vide et ses inclusions, ainsi que `public/css/dashboard-reference.css` ont été retirés. Aucune autre version de paquet n’a été mise à jour; la découverte Composer a été régénérée et les routes restent à 163.
- L’audit a aussi repéré deux squelettes Laravel non activés : `TrustHosts` n’était ni dans le Kernel ni ailleurs; `BroadcastServiceProvider` était explicitement désactivé et son seul fichier de canaux n’était chargé par aucun provider. Suppression dans le second lot de ménage.
- `guzzlehttp/guzzle` était uniquement une dépendance Composer directe et son alias `Http` n’était appelé nulle part dans les sources. Retrait et suppression de ses dépendances exclusives dans le second lot de ménage.
- Le test `tests/Unit/ExampleTest.php` ne vérifiait que `assertTrue(true)`; il a été supprimé et le dossier `Unit` vide a été retiré des tests PHPUnit. Le test fonctionnel de `/` est conservé.
- `laravel/sail` était le seul outillage Docker déclaré, sans fichier Docker/Compose ni référence au script Sail dans le dépôt; retiré des dépendances de développement. `laravel/tinker` reste disponible comme outil CLI.
- Contrôles du premier ménage : `php artisan route:list` réussit et conserve 163 routes; `php artisan view:clear` réussit; `npm run dev` réussit. La suite comptait alors 34 réussis / 7 échoués (41 au total).
- Contrôles du second ménage : `composer validate` est valide, `php artisan route:list` garde 163 routes, `php artisan view:clear` et `npm run dev` réussissent. Après retrait du test unitaire vide, la suite compte 33 réussis / 7 échoués (40 au total), les mêmes sept erreurs préexistantes dans `MemorialAndChefTest` et `TourTest`.
- Composer a signalé 38 avis de vulnérabilité touchant 9 paquets déjà verrouillés. Aucun paquet restant n’a été mis à jour dans ce ménage; cet audit de sécurité devra être traité séparément.
- Le SmokeTest ajouté dans `6b0e985` passe. Il couvre 22 pages avec un compte chef et un monde synthétiques.
- `git diff --stat avant-menage` : 148 fichiers, 159 insertions, 25 768 suppressions.

## Annulation

- Annuler une catégorie : `git revert <hash-du-commit>`.
- Revenir au point initial du ménage : créer une branche ou réinitialiser le travail local sur le tag `avant-menage` (ne pas réécrire l’historique partagé).

## Non réalisé

- Aucun fichier local `.env` ou SQL n’a été modifié; le dump SQL ne sera que retiré de l’index.
- Aucune table, colonne ou migration ne sera supprimée ou modifiée.
- Les éléments PROBABLES/À VÉRIFIER seront conservés et documentés.
