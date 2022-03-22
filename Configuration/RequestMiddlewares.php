<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

return [
    'backend' => [
        'buepro/timelog/mediator' => [
            'target' => \Buepro\Timelog\Middleware\Backend\MediatorMiddleware::class,
            'after' => [
                'typo3/cms-core/response-propagation',
                'typo3/cms-backend/site-resolver',
                'typo3/cms-backend/response-headers',
                'typo3/cms-extbase/signal-slot-deprecator'
            ],
        ],
    ],
    'frontend' => [
        'buepro/timelog/send-mail' => [
            'target' => \Buepro\Timelog\Middleware\Frontend\SendMailMiddleware::class,
            'after' => [
                'typo3/cms-frontend/prepare-tsfe-rendering',
            ],
            'before' => [
                'typo3/cms-frontend/shortcut-and-mountpoint-redirect',
            ],
        ]
    ]
];
