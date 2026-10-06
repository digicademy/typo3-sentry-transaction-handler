<?php

declare(strict_types=1);

use Digicademy\Typo3SentryTransactionHandler\Middleware\TransactionMiddleware;

return [
    'frontend' => [
        'digicademy/sentry-transaction-handler' => [
            'target' => TransactionMiddleware::class,
            'before' => [
                'typo3/cms-frontend/site',
            ],
        ],
    ],
    'backend' => [
        'digicademy/sentry-transaction-handler' => [
            'target' => TransactionMiddleware::class,
            'before' => [
                'typo3/cms-backend/backend-routing',
            ],
        ],
    ],
];
