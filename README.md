# LoreRoom

LoreRoom est une application web de gestion d'univers narratifs. Elle permet de créer des mondes, de structurer des personnages, d'organiser leurs relations et de suivre l'évolution d'une chronologie dans un même espace de travail.

## Objectif du projet

L'application a pour objectif d'aider à la création et à l'organisation d'un univers de fiction en centralisant :

- les mondes ;
- les personnages ;
- les relations ;
- les événements ;
- les lieux ;
- les factions ;
- les métiers ;
- les espèces ;
- le lore ;
- les médias associés.

## Fonctionnalités principales

- authentification et gestion de session ;
- gestion multi-mondes avec sélection du monde actif ;
- tableau de bord de suivi ;
- fiches personnages détaillées ;
- relations entre personnages ;
- arbre généalogique ;
- chroniques et frises temporelles ;
- gestion des lieux ;
- gestion des factions et organisations ;
- gestion des métiers ;
- gestion des espèces ;
- gestion du lore ;
- galerie d'images ;
- export PDF des fiches personnages ;
- API REST pour plusieurs entités métier ;
- module d'administration des comptes.

## Modules de l'application

### Authentification et sessions

Ce module gère l'inscription, la connexion, la déconnexion et la sécurité de session.

Résultat attendu :
l'utilisateur accède à un espace sécurisé, avec une expiration automatique en cas d'inactivité.

### Mondes

Ce module permet de créer, modifier, consulter, supprimer et sélectionner un monde actif.

Résultat attendu :
l'utilisateur peut travailler dans plusieurs univers distincts tout en gardant un contexte clair.

### Tableau de bord

Le tableau de bord centralise les informations importantes : statistiques, anniversaires, événements du jour et chroniques à venir.

Résultat attendu :
une vue synthétique de l'activité du monde sélectionné.

### Personnages

Ce module permet de créer des fiches complètes : identité, apparence, objectifs, secrets, voix, médias, études, métiers, espèces et galerie.

Résultat attendu :
chaque personnage dispose d'une fiche exploitable pour la narration et la documentation.

### Relations

Ce module gère les liens familiaux, sociaux et affectifs entre personnages.

Résultat attendu :
les relations sont organisées, lisibles et cohérentes dans l'ensemble du projet.

### Arbre généalogique

Ce module construit une représentation visuelle des filiations, des couples et des générations.

Résultat attendu :
une lecture rapide de la structure familiale d'un groupe de personnages.

### Chroniques

Ce module enregistre les événements datés, les relie à des personnages et à des lieux, puis les affiche dans des frises.

Résultat attendu :
le suivi de l'évolution narrative de l'univers dans le temps.

### Lieux

Ce module gère les lieux d'un monde avec leurs types, régions, descriptions et médias.

Résultat attendu :
une géographie structurée et réutilisable dans les autres modules.

### Factions

Ce module permet de gérer des organisations, leurs membres, leurs relations et leurs diplômes.

Résultat attendu :
les groupes d'influence du monde sont documentés avec leurs rôles et interactions.

### Métiers

Ce module permet de définir des métiers par défaut ou spécifiques à un monde.

Résultat attendu :
les personnages peuvent être reliés à des fonctions ou parcours professionnels cohérents.

### Espèces

Ce module décrit les espèces, leurs caractéristiques, capacités, origine et durée de vie.

Résultat attendu :
une base claire pour les composantes biologiques ou fantastiques de l'univers.

### Lore

Ce module centralise les connaissances du monde : culture, religion, politique, mythologie, technologie et règles de l'univers.

Résultat attendu :
un référentiel de contenu garantissant la cohérence globale du projet.

### Galerie

Ce module regroupe les portraits et les images complémentaires liées aux personnages.

Résultat attendu :
les ressources visuelles sont faciles à retrouver et à consulter.

### Suggestions

Ce module permet l'envoi de retours ou de propositions d'amélioration.

Résultat attendu :
l'application peut recueillir les besoins d'évolution ou les remarques d'usage.

### Administration des comptes

Ce module, réservé à l'administrateur, permet de consulter tous les comptes, de modifier leur rôle et de supprimer un utilisateur sous conditions.

Résultat attendu :
une gestion simple des accès et des profils utilisateurs.

## Technologies utilisées

- PHP ;
- Laravel 8 ;
- Blade ;
- JavaScript ;
- MySQL ;
- Laravel Sanctum ;
- DomPDF ;
- PHPUnit ;
- Composer ;
- npm ;
- Laravel Mix.

## Prérequis

Pour exécuter le projet localement, il faut disposer de :

- PHP 7.3 minimum ou d'une version 8 compatible ;
- Composer ;
- Node.js et npm ;
- MySQL ;
- un environnement local de type WAMP, XAMPP ou équivalent.

## Installation

1. Installer les dépendances PHP :
   `composer install`
2. Installer les dépendances front-end :
   `npm install`
3. Copier le fichier d'environnement :
   `copy .env.example .env`
4. Configurer les variables du fichier `.env`, notamment `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` et `APP_URL`.
5. Générer la clé de l'application :
   `php artisan key:generate`
6. Lancer les migrations :
   `php artisan migrate`
7. Créer le lien de stockage public :
   `php artisan storage:link`
8. Compiler les ressources front-end :
   `npm run dev`
9. Démarrer le serveur :
   `php artisan serve`

## Accès à l'application

- page de connexion : `/login`
- espace de gestion : `/manage`

## Scripts utiles

- `npm run dev` : compilation de développement ;
- `npm run watch` : surveillance des fichiers ;
- `npm run prod` : compilation de production.

## Sécurité et sessions

La session expire après 15 minutes d'inactivité, selon la valeur définie dans `SESSION_LIFETIME`.

Des middlewares contrôlent notamment :

- la validité de la session utilisateur ;
- la présence d'un monde actif ;
- la cohérence du jeton temporaire de connexion ;
- l'accès administrateur aux modules réservés.

## Structure fonctionnelle

- mondes : `manage/worlds`
- personnages : `manage/characters`
- arbre généalogique : `manage/arbre-genealogique`
- relations : `manage/relations`
- chroniques : `manage/chronicles`
- lieux : `manage/places`
- factions : `manage/factions`
- métiers : `manage/jobs`
- espèces : `manage/species`
- lore : `manage/lore`
- galerie : `manage/galerie`
- suggestions : `manage/suggestions`
- comptes utilisateurs : `manage/users`

## Description des ressources documentaires

Les ressources documentaires utilisées dans ce projet sont principalement internes au dépôt :

- le `README.md`, qui présente l'objectif de l'application, son installation et son organisation générale ;
- les fichiers de routes, qui décrivent les parcours applicatifs et les points d'entrée ;
- les contrôleurs, qui traduisent la logique métier ;
- les modèles, qui représentent les entités manipulées par l'application ;
- les migrations de base de données, qui documentent la structure du schéma relationnel ;
- les fichiers SQL présents dans le projet, qui servent de support de structure ou de données ;
- les vues Blade, qui documentent l'interface et les écrans disponibles ;
- les fichiers de configuration Laravel, qui décrivent l'environnement technique et les services utilisés.

Ces ressources permettent de comprendre le fonctionnement de l'application, son architecture, ses modules et ses contraintes d'exécution.

## Description des ressources matérielles et logicielles utilisées

### Ressources matérielles

Le projet est conçu pour être développé et exécuté sur un poste de travail classique disposant :

- d'un processeur capable d'exécuter PHP, MySQL et un serveur web local ;
- d'une mémoire vive suffisante pour Apache, PHP, MySQL et Node.js ;
- d'un espace de stockage pour le code source, les dépendances, la base de données et les médias ;
- d'un navigateur web pour l'utilisation de l'application.

### Ressources logicielles

Les principales ressources logicielles mobilisées sont :

- un système d'exploitation compatible avec PHP, MySQL et Node.js ;
- un environnement serveur local, ici de type WAMP ;
- Laravel pour le développement back-end ;
- Blade pour le rendu des vues ;
- MySQL pour la persistance des données ;
- Laravel Mix pour la compilation des ressources ;
- Composer pour la gestion des dépendances PHP ;
- npm pour la gestion des dépendances front-end ;
- PHPUnit pour les tests ;
- DomPDF pour l'export PDF ;
- Sanctum pour l'authentification API.
