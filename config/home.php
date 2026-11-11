<?php

return [
    'incomplete' => [
        'characters' => ['label' => 'Personnage', 'field' => 'birth_date', 'missing' => 'date de naissance', 'route' => 'manage.characters.show', 'name' => 'display_name'],
        'places' => ['label' => 'Lieu', 'field' => 'description', 'missing' => 'description', 'route' => 'manage.places.show', 'name' => 'name'],
        'factions' => ['label' => 'Faction', 'field' => 'summary', 'missing' => 'résumé', 'route' => 'manage.factions.show', 'name' => 'name'],
        'species' => ['label' => 'Espèce', 'field' => 'characteristics', 'missing' => 'caractéristiques', 'route' => 'manage.species.show', 'name' => 'name'],
        'jobs' => ['label' => 'Métier', 'field' => 'description', 'missing' => 'description', 'route' => 'manage.jobs.show', 'name' => 'name'],
        'lore_entries' => ['label' => 'Lore', 'field' => 'content', 'missing' => 'contenu', 'route' => 'manage.lore.show', 'name' => 'title'],
    ],
];
