<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_taskgroup',
        'label' => 'title',
        'label_userFunc' => 'Buepro\\Timelog\\Backend\\UserFunc\\TcaUserFunc->getTaskGroupLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
        ],
        'searchFields' => 'handle,title,description',
        'default_sortby' => 'tstamp DESC',
        'iconfile' => 'EXT:timelog/Resources/Public/Icons/domain-model-taskgroup.svg'
    ],
    'palettes' => [
        'texts' => [
            'showitem' => 'description, internal_note',
        ],
        'times' => [
            'showitem' => 'time_target, time_deviation, --linebreak--, active_time, heap_time, batch_time',
        ]
    ],
    'types' => [
        '1' => [
            'showitem' =>
                '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    project,
                    title,
                    --palette--;;texts,
                    --palette--;;times,
                    tasks,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, handle',
        ]
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
        'handle' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:handle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => 1
            ],
        ],
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:title',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim'
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'internal_note' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:internal_note',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'time_target' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_taskgroup.time_target',
            'config' => [
                'type' => 'input',
                'size' => 12,
                'eval' => 'double2'
            ]
        ],
        'time_deviation' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_taskgroup.time_deviation',
            'config' => [
                'type' => 'input',
                'size' => 12,
                'eval' => 'double2',
                'readOnly' => 1
            ]
        ],
        'active_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:active_time',
            'config' => [
                'type' => 'input',
                'size' => 12,
                'eval' => 'double2',
                'readOnly' => 1
            ]
        ],
        'heap_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:heap_time',
            'config' => [
                'type' => 'input',
                'size' => 12,
                'eval' => 'double2',
                'readOnly' => 1
            ]
        ],
        'batch_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:batch_time',
            'config' => [
                'type' => 'input',
                'size' => 12,
                'eval' => 'double2',
                'readOnly' => 1
            ]
        ],
        'project' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_timelog_domain_model_project',
                'size' => 1,
                'readOnly' => 1
            ],
        ],
        'tasks' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tasks',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_timelog_domain_model_task',
                'foreign_field' => 'task_group',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],

        ],
    ],
];
