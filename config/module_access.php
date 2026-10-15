<?php

return [
    'public_modules' => [
        'dashboard',
        'account',
        'worlds',
        'characters',
        'chapters',
        'places',
        'chronicles',
        'relations',
    ],

    'roles' => [
        'reader' => [
            'label' => 'app.roles.reader.label',
            'help' => 'app.roles.reader.help',
        ],
        'creator' => [
            'label' => 'app.roles.creator.label',
            'help' => 'app.roles.creator.help',
        ],
        'librarian' => [
            'label' => 'app.roles.librarian.label',
            'help' => 'app.roles.librarian.help',
        ],
        'moderator' => [
            'label' => 'app.roles.moderator.label',
            'help' => 'app.roles.moderator.help',
        ],
        'analyst' => [
            'label' => 'app.roles.analyst.label',
            'help' => 'app.roles.analyst.help',
        ],
    ],
    'signup_roles' => [
        'scenariste' => [
            'label' => 'app.signup_roles.scenariste.label',
            'help' => 'app.signup_roles.scenariste.help',
        ],
        'artiste' => [
            'label' => 'app.signup_roles.artiste.label',
            'help' => 'app.signup_roles.artiste.help',
        ],
    ],

    'modules' => [
        'jobs' => ['roles' => ['librarian']],
        'lore' => ['roles' => ['librarian']],
        'species' => ['roles' => ['librarian']],
        'factions' => ['roles' => ['creator']],
        'gallery' => ['roles' => ['creator']],
        'genealogy' => ['roles' => ['creator']],
        'suggestions' => ['roles' => ['moderator']],
        'trash' => ['roles' => ['moderator']],
        'analytics' => ['roles' => ['analyst']],
        'users' => ['roles' => ['admin']],
    ],
];
