<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

call_user_func(function () {
    $fieldModification = [
        'ctrl' => [
            'label' => 'title',
            'label_alt' => 'title,client',
            'label_alt_force' => 1,
            'label_userFunc' => 'Buepro\\Timelog\\Backend\\UserFunc\\TcaUserFunc->getProjectLabel',
            'searchFields' => 'title',
            'default_sortby' => 'tstamp DESC',
            'iconfile' => 'EXT:timelog/Resources/Public/Icons/tx_timelog_domain_model_project.svg'
        ],
        'interface' => [
            'showRecordFieldList' =>
                'client, owner, title, description, active_time, heapTime, batchTime, handle'
        ],
        'palettes' => [
            'references' => [
                'showitem' => 'client, owner',
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
                        title, description,
                        --palette--;;times, 
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, handle',
            ]
        ],
        'columns' => [
            'client' => [
                'config' => [
                    'type' => 'group',
                    'renderType' => '',
                    'internal_type' => 'db',
                    'allowed' => 'fe_users',
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
                'config' => [
                    'type' => 'group',
                    'renderType' => '',
                    'internal_type' => 'db',
                    'allowed' => 'fe_users',
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
                ]
            ],
            'active_time' => [
                'config' => [
                    'size' => 12,
                    'eval' => 'double2',
                    'readOnly' => 1
                ]
            ],
            'heap_time' => [
                'config' => [
                    'size' => 12,
                    'eval' => 'double2',
                    'readOnly' => 1
                ]
            ],
            'batch_time' => [
                'config' => [
                    'size' => 12,
                    'eval' => 'double2',
                    'readOnly' => 1
                ]
            ],
            'handle' => [
                'config' => [
                    'readOnly' => 1
                ]
            ]
        ]
    ];
    $GLOBALS['TCA']['tx_timelog_domain_model_project'] = array_replace_recursive(
        $GLOBALS['TCA']['tx_timelog_domain_model_project'],
        $fieldModification
    );
});
