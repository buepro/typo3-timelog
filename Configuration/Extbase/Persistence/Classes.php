<?php

declare(strict_types = 1);

return [
    \Buepro\Timelog\Domain\Model\Client::class => [
        'tableName' => 'fe_users',
        'properties' => [
            'ownerEmail' => [
                'fieldName' => 'tx_timelog_owner_email',
            ],
        ],
    ],
];
