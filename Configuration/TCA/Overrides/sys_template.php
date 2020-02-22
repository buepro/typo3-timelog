<?php

defined('TYPO3_MODE') || die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(function () {
    ExtensionManagementUtility::addStaticFile('timelog', 'Configuration/TypoScript', 'Timelog');
})();
