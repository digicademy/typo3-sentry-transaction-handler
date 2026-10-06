<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Sentry Transaction Handler',
    'description' => 'Opens a Sentry transaction per TYPO3 request so traces_sample_rate actually produces performance data.',
    'category' => 'plugin',
    'author' => 'Frodo Podschwadek',
    'author_email' => 'frodo.podschwadek@adwmainz.de',
    'author_company' => 'Academy of Sciences and Literature | Mainz',
    'state' => 'stable',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-14.99.99',
            'sentry_client' => '6.0.0-6.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
