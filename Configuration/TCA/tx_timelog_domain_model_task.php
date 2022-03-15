<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

return [
    'ctrl' => [
        'title' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_task',
        'label' => 'title',
        'label_userFunc' => 'Buepro\\Timelog\\Backend\\UserFunc\\TcaUserFunc->getTaskLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
        ],
        'searchFields' => 'handle,title,description',
        'default_sortby' => 'crdate DESC',
        'iconfile' => 'EXT:timelog/Resources/Public/Icons/domain-model-task.svg'
    ],
    'palettes' => [
        'batch' => [
            'showitem' => 'batch_date'
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    project, worker, task_group, title, description, active_time, intervals,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, handle,
                    --palette--;;batch',
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
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_task.handle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => 1
            ],
        ],
        'title' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_task.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'description' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_task.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'active_time' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_task.active_time',
            'config' => [
                'type' => 'input',
                'size' => 12,
                'eval' => 'double2',
                'readOnly' => 1
            ]
        ],
        'batch_date' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_task.batch_date',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 14,
                'eval' => 'datetime',
                'default' => 0
            ],
        ],
        'project' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_task.project',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_timelog_domain_model_project',
                'foreign_table_where' => 'ORDER BY tx_timelog_domain_model_project.tstamp DESC',
                'default' => 0,
                'size' => 3,
                'autoSizeMax' => 5,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'task_group' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_taskgroup',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_timelog_domain_model_taskgroup',
                'foreign_table_where' => '{#tx_timelog_domain_model_taskgroup}.{#project} = ###REC_FIELD_project### ORDER BY tx_timelog_domain_model_taskgroup.tstamp DESC',
                'default' => 0,
                'size' => 3,
                'autoSizeMax' => 5,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'worker' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_task.worker',
            'config' => [
                'type' => 'group',
                'renderType' => '',
                'foreign_table' => 'fe_users',
                'internal_type' => 'db',
                'allowed' => 'fe_users',
                'default' => 0,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'fieldControl' => [
                    'addRecord' => [
                        'disabled' => false,
                    ],
                    'listModule' => [
                        'disabled' => false,
                    ],
                    'elementBrowser' => [
                        'disabled' => true,
                    ],
                ],
                'fieldWizard' => [
                    'recordsOverview' => [
                        'disabled' => true,
                    ],
                ],
            ],
        ],
        'intervals' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_task.intervals',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_timelog_domain_model_interval',
                'foreign_field' => 'task',
                'foreign_default_sortby' => 'start_time DESC',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 0,
                    'expandSingle' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],

        ],
    ],
];
