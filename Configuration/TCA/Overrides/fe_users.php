<?php

defined('TYPO3') || die('Access denied.');

(function () {
    $columns = [
        'tx_timelog_owner_email' => [
            'exclude' => true,
            'label' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:fe_users.owner_email',
            'description' => 'LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:fe_users.owner_email.description',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim',
                'max' => 255
            ]
        ]
    ];
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $columns);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'fe_users', 'tx_timelog_owner_email', '', 'after:email');
})();
