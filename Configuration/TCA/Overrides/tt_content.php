<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3_MODE') || die();

(function () {
    $version = \TYPO3\CMS\Core\Utility\VersionNumberUtility::getCurrentTypo3Version();
    $version = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger($version);
    if ($version < 10000000) {
        // @extensionScannerIgnoreLine
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Buepro.Timelog',
            'Taskpanel',
            'Task panel'
        );
    } else {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Timelog',
            'Taskpanel',
            'Task panel'
        );
    }
})();
