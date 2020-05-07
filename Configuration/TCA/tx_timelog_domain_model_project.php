<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project',
        'label' => 'handle',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
        ],
        'searchFields' => 'handle,title,description',
        'iconfile' => 'EXT:timelog/Resources/Public/Icons/tx_timelog_domain_model_project.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'handle, title, description, active_time, heap_time, batch_time, client, owner'],
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
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.handle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'active_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.active_time',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ]
        ],
        'heap_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.heap_time',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ]
        ],
        'batch_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.batch_time',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ]
        ],
        'client' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.client',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'owner' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.owner',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'tasks' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.tasks',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_timelog_domain_model_task',
                'foreign_field' => 'project',
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
