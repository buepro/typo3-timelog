<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_interval',
        'label' => 'start_time',
        'label_userFunc' => 'Buepro\\Timelog\\Backend\\UserFunc\\TcaUserFunc->getIntervalLabel',
        'default_sortby' => 'tstamp DESC',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
        ],
        'searchFields' => '',
        'iconfile' => 'EXT:timelog/Resources/Public/Icons/domain-model-interval.svg'
    ],
    'palettes' => [
        'time' => [
            'showitem' => 'start_time, end_time, duration',
        ]
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, --palette--;;time'
        ],
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'start_time' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_interval.start_time',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 12,
                'eval' => 'datetime',
                'default' => time()
            ],
        ],
        'end_time' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_interval.end_time',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 12,
                'eval' => 'datetime',
                'default' => 0
            ],
        ],
        'duration' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_interval.duration',
            'config' => [
                'type' => 'input',
                'size' => 12,
                'eval' => 'double2',
                'readOnly' => 1
            ]
        ],
        'task' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
