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
        'label_alt' => 'title,client',
        'label_alt_force' => 1,
        'label_userFunc' => 'Buepro\\Timelog\\Backend\\UserFunc\\TcaUserFunc->getProjectLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
        ],
        'searchFields' => 'handle,title,description',
        'default_sortby' => 'tstamp DESC',
        'iconfile' => 'EXT:timelog/Resources/Public/Icons/domain-model-project.svg'
    ],
    'palettes' => [
        'references' => [
            'showitem' => 'client, owner, --linebreak--, cc_email',
        ],
        'texts' => [
            'showitem' => 'description, internal_note',
        ],
        'times' => [
            'showitem' => 'active_time, heap_time, batch_time',
        ]
    ],
    'types' => [
        '1' => [
            'showitem' =>
                '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                        --palette--;;references,
                        title,
                        --palette--;;texts,
                        --palette--;;times,
                        tasks,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended, task_groups,
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
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.handle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'readOnly' => 1
            ],
        ],
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.title',
            'config' => [
                'type' => 'input',
                'size' => 40,
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
        'internal_note' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.internal_note',
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
                'size' => 12,
                'eval' => 'double2',
                'readOnly' => 1
            ]
        ],
        'heap_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.heap_time',
            'config' => [
                'type' => 'input',
                'size' => 12,
                'eval' => 'double2',
                'readOnly' => 1
            ]
        ],
        'batch_time' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.batch_time',
            'config' => [
                'type' => 'input',
                'size' => 12,
                'eval' => 'double2',
                'readOnly' => 1
            ]
        ],
        'client' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.client',
            'config' => [
                'type' => 'group',
                'renderType' => '',
                'internal_type' => 'db',
                'allowed' => 'fe_users',
                'foreign_table' => 'fe_users',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'default' => 0,
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
                'suggestOptions' => [
                    'fe_users' => [
                        'additionalSearchFields' => 'first_name,last_name,name,company',
                        'renderFunc' => 'Buepro\\Timelog\\Backend\\UserFunc\\TcaUserFunc->getFeUsersLabel',
                    ],
                ],
            ],
        ],
        'owner' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.owner',
            'config' => [
                'type' => 'group',
                'renderType' => '',
                'internal_type' => 'db',
                'allowed' => 'fe_users',
                'foreign_table' => 'fe_users',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'default' => 0,
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
                'suggestOptions' => [
                    'fe_users' => [
                        'additionalSearchFields' => 'first_name,last_name,name,company',
                        'renderFunc' => 'Buepro\\Timelog\\Backend\\UserFunc\\TcaUserFunc->getFeUsersLabel',
                    ],
                ],
            ],
        ],
        'cc_email' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.cc_email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'tasks' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tasks',
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
        'task_groups' => [
            'exclude' => false,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_domain_model_project.task_groups',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_timelog_domain_model_taskgroup',
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
