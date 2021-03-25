<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3') || die('Access denied.');

(function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'timelog',
        'Configuration/TypoScript',
        'Timelog'
    );
})();
