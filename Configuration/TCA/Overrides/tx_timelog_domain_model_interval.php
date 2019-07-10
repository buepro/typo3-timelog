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
            'label' => 'start_time',
            'label_userFunc' => 'Buepro\\Timelog\\Backend\\UserFunc\\TcaUserFunc->getIntervalLabel',
            'default_sortby' => 'tstamp DESC',
            'iconfile' => 'EXT:timelog/Resources/Public/Icons/tx_timelog_domain_model_interval.svg'
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
            'start_time' => [
                'config' => [
                    'size' => 12,
                ]
            ],
            'end_time' => [
                'config' => [
                    'size' => 12,
                    'default' => 0,
                ]
            ],
            'duration' => [
                'config' => [
                    'size' => 12,
                    'eval' => 'double2',
                    'readOnly' => 1
                ]
            ]
        ]
    ];
    $GLOBALS['TCA']['tx_timelog_domain_model_interval'] = array_replace_recursive(
        $GLOBALS['TCA']['tx_timelog_domain_model_interval'],
        $fieldModification
    );
});
