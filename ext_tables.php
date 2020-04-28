<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3_MODE') || die('Access denied.');

(function () {
    $version = \TYPO3\CMS\Core\Utility\VersionNumberUtility::getCurrentTypo3Version();
    $version = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger($version);
    if ($version < 10000000) {
        // For TYPO3 < V10
        /** @extensionScannerIgnoreLine */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Buepro.Timelog',
            'Taskpanel',
            'Task panel'
        );
    } else {
        // For TYPO3 V10
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Timelog',
            'Taskpanel',
            'Task panel'
        );
    }

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_timelog_domain_model_task', 'EXT:timelog/Resources/Private/Language/locallang_csh_tx_timelog_domain_model_task.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_timelog_domain_model_task');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_timelog_domain_model_interval', 'EXT:timelog/Resources/Private/Language/locallang_csh_tx_timelog_domain_model_interval.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_timelog_domain_model_interval');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_timelog_domain_model_project', 'EXT:timelog/Resources/Private/Language/locallang_csh_tx_timelog_domain_model_project.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_timelog_domain_model_project');
})();
