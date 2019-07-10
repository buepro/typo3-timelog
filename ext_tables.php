<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    static function () {
        ExtensionUtility::registerPlugin(
            'Buepro.Timelog',
            'Taskpanel',
            'Task panel'
        );

        ExtensionManagementUtility::addStaticFile('timelog', 'Configuration/TypoScript', 'Timelog');

        ExtensionManagementUtility::addLLrefForTCAdescr('tx_timelog_domain_model_task', 'EXT:timelog/Resources/Private/Language/locallang_csh_tx_timelog_domain_model_task.xlf');
        ExtensionManagementUtility::allowTableOnStandardPages('tx_timelog_domain_model_task');

        ExtensionManagementUtility::addLLrefForTCAdescr('tx_timelog_domain_model_interval', 'EXT:timelog/Resources/Private/Language/locallang_csh_tx_timelog_domain_model_interval.xlf');
        ExtensionManagementUtility::allowTableOnStandardPages('tx_timelog_domain_model_interval');

        ExtensionManagementUtility::addLLrefForTCAdescr('tx_timelog_domain_model_project', 'EXT:timelog/Resources/Private/Language/locallang_csh_tx_timelog_domain_model_project.xlf');
        ExtensionManagementUtility::allowTableOnStandardPages('tx_timelog_domain_model_project');
    }
);
