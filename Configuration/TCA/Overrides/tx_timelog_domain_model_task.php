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
            'label_userFunc' => 'Buepro\\Timelog\\Backend\\UserFunc\\TcaUserFunc->getTaskLabel',
            'searchFields' => 'handle,title,description',
            'default_sortby' => 'crdate DESC',
            'iconfile' => 'EXT:timelog/Resources/Public/Icons/tx_timelog_domain_model_task.svg'
        ],
        'palettes' => [
            'batch' => [
                'showitem' => 'batch_date'
            ],
        ],
        'types' => [
            '1' => [
                'showitem' =>
                    '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                        project, worker, title, description, active_time, intervals, 
                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, handle, 
                        --palette--;;batch',
            ]
        ],
        'columns' => [
//            'project' => [
//                'config' => [
//                    'type' => 'group',
//                    'renderType' => '',
//                    'internal_type' => 'db',
//                    'allowed' => 'tx_timelog_domain_model_project',
//                    'default' => 0,
//                    'size' => 1,
//                    'minitems' => 0,
//                    'maxitems' => 1,
//                    'fieldControl' => [
//                        'addRecord' => [
//                            'disabled' => false,
//                        ],
//                        'listModule' => [
//                            'disabled' => false,
//                        ],
//                        'elementBrowser' => [
//                            'disabled' => true,
//                        ],
//                    ],
//                    'fieldWizard' => [
//                        'recordsOverview' => [
//                            'disabled' => true,
//                        ],
//                    ],
//                ]
//            ],
            'project' => [
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectMultipleSideBySide',
                    'foreign_table_where' => 'ORDER BY tx_timelog_domain_model_project.tstamp DESC',
                    'default' => 0,
                    'size' => 3,
                    'autoSizeMax' => 5,
                ],
            ],
            'worker' => [
                'config' => [
                    'type' => 'group',
                    'renderType' => '',
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
                ]
            ],
            'intervals' => [
                'config' => [
                    'appearance' => [
                        'collapseAll' => 0,
                        'expandSingle' => 0,
                    ],
                    'foreign_default_sortby' => 'start_time DESC',
                ]
            ],
            'active_time' => [
                'config' => [
                    'size' => 12,
                    'eval' => 'double2',
                    'default' => 0,
                    'readOnly' => 1
                ]
            ],
            'batch_date' => [
                'config' => [
                    'default' => 0,
                    'size' => 14,
                ],
            ],
            'handle' => [
                'config' => [
                    'readOnly' => 1
                ]
            ]
        ]
    ];
    $GLOBALS['TCA']['tx_timelog_domain_model_task'] = array_replace_recursive(
        $GLOBALS['TCA']['tx_timelog_domain_model_task'],
        $fieldModification
    );
});
