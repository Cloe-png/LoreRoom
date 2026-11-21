# Revue de préparation production

## Faits vérifiés

- La branche `menage` a été poussée sur `origin` avant cette revue dans le commit `5e41f71`.
- Le projet a été mis à niveau de Laravel 8 vers Laravel 13.43, avec PHP 8.3 minimum, Sanctum 4.5, DomPDF 3.1, PHPUnit 12 et Symfony 7.4. Les paquets CORS et Ignition abandonnés ont été retirés.
- `composer audit` : aucun avis connu sur les dépendances résolues. `npm audit --omit=dev` : aucune vulnérabilité de dépendance de production.
- Les routes API demandent Sanctum et contrôlent la propriété des mondes et objets associés. Les listes sont limitées aux mondes du compte connecté. Les accès sans authentification et les accès à un autre monde sont vérifiés par `ApiOwnershipTest`.
- Les routes de connexion et d’inscription sont limitées à dix requêtes par minute. Les origines CORS externes sont refusées par défaut ; `CORS_ALLOWED_ORIGINS` permet d’ajouter des origines explicites.
- La route de médias requiert une session authentifiée, vérifie que le fichier appartient à un monde du compte connecté, rejette les chemins de traversée et marque les réponses comme privées.
- Laravel utilise PDO pour MySQL via Eloquent et le Query Builder. L’extension `pdo_mysql` est installée sur l’environnement de développement.
- `artisan migrate:status` a confirmé que les migrations du dossier sont appliquées dans la base locale. Aucune ligne ou table de production n’a été effacée, aucun dump SQL n’a été ajouté au dépôt.

## Médias et fichiers

- Logos d’interface : `public/images/brand/loreroom.png`.
- Fonds : `public/images/backgrounds/`.
- Cadre et portrait par défaut : `public/images/museum/`.
- Anciennes illustrations et éléments non utilisés conservés hors du webroot dans `resources/images/archive/`.
- Les copies racine en double du cadre et de l’image d’aide, ainsi que le favicon vide, ont été supprimés. Toutes les références actives ont été mises à jour.
- La page statique du compte à rebours est maintenant dans `public/Coming_Soon.html`.

## Base de données

Les migrations et tables historiques sont conservées : les supprimer ou supprimer les colonnes repérées comme peu utilisées risquerait de perdre des données ou de casser les installations existantes. Deux migrations historiques contenaient des `ALTER TABLE ... MODIFY` MySQL sans garde de pilote ; elles sont désormais conditionnelles à MySQL et la migration fraîche crée `user_logs.action_user` comme texte extensible. Aucune opération de suppression de données n’a été faite.

## Vérifications restantes avant ouverture publique

- La dernière suite locale complète compte 49 tests réussis et 2 échecs d’attentes de vue : une assertion de date sur le bouton de commémoration et une attente d’absence du mot « Chronologie », qui apparaît aussi dans le menu de navigation sans monde. Le test média et les tests de propriété API réussissent. La protection d’inactivité est exclue uniquement du test `MemorialAndChefTest`, qui manipule l’horloge; elle reste active dans l’application.
- `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY`, base distante, SMTP, HTTPS et cookie de session sécurisé devront être configurés sur o2switch. Aucun déploiement externe n’a été effectué.
- Lire `DEPLOYMENT_O2SWITCH.md` et vérifier la configuration PHP et le chemin webroot dans cPanel avant la première mise en ligne.
