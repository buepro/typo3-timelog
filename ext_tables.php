<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3') || die('Access denied.');

(function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_timelog_domain_model_project', 'EXT:timelog/Resources/Private/Language/locallang_csh_tx_timelog_domain_model_project.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_timelog_domain_model_project');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_timelog_domain_model_taskgroup');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_timelog_domain_model_task', 'EXT:timelog/Resources/Private/Language/locallang_csh_tx_timelog_domain_model_task.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_timelog_domain_model_task');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_timelog_domain_model_interval', 'EXT:timelog/Resources/Private/Language/locallang_csh_tx_timelog_domain_model_interval.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_timelog_domain_model_interval');
})();
