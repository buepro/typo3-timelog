<?php

declare(strict_types = 1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

return [
    \Buepro\Timelog\Domain\Model\FrontendUser::class => [
        'tableName' => 'fe_users',
    ],
    \Buepro\Timelog\Domain\Model\Client::class => [
        'tableName' => 'fe_users',
        'properties' => [
            'ownerEmail' => [
                'fieldName' => 'tx_timelog_owner_email',
            ],
        ],
    ],
];
