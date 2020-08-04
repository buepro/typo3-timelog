<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3_MODE') || die('Access denied.');

(function () {
    /**
     * Configure plugin
     */
    $version = \TYPO3\CMS\Core\Utility\VersionNumberUtility::getCurrentTypo3Version();
    $version = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger($version);
    if ($version < 10000000) {
        // For TYPO3 < V10
        // @extensionScannerIgnoreLine
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Buepro.Timelog',
            'Taskpanel',
            [
                'Task' => 'list,createBatch,error',
                'Project' => 'list'
            ],
            // non-cacheable actions
            [
                'Task' => 'list,createBatch,error',
                'Project' => 'list'
            ]
        );
    } else {
        // For TYPO3 V10
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Timelog',
            'Taskpanel',
            [
                \Buepro\Timelog\Controller\TaskController::class => 'list,createBatch,error',
                \Buepro\Timelog\Controller\ProjectController::class => 'list'
            ],
            // non-cacheable actions
            [
                \Buepro\Timelog\Controller\TaskController::class => 'list,createBatch,error',
                \Buepro\Timelog\Controller\ProjectController::class => 'list'
            ]
        );
    }

    /**
     * New content element wizard
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    taskpanel {
                        iconIdentifier = tx-timelog-plugin-taskpanel
                        title = LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_taskpanel.name
                        description = LLL:EXT:timelog/Resources/Private/Language/locallang_db.xlf:tx_timelog_taskpanel.description
                        tt_content_defValues {
                            CType = list
                            list_type = timelog_taskpanel
                        }
                    }
                }
                show = *
            }
        }'
    );

    /**
     * Extension configuration
     */
    if (1) {
        $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
        );

        // Sets hashid salt
        $timelogConfiguration = $extensionConfiguration->get('timelog');
        if ($timelogConfiguration['hashidSalt'] === '') {
            $extensionConfiguration->set('timelog', 'hashidSalt', md5(
                sprintf('%d Lihdfg!', time())
            ));
        }
    }

    /**
     * Page TS
     */
    if (1) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:timelog/Configuration/TsConfig/Page/TCEMAIN.tsconfig">'
        );
    }

    /**
     * Register Icons
     */
    if (1) {
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $icons = [
            'plugin-taskpanel',
            'domain-model-project',
            'domain-model-task',
            'domain-model-interval',
            'domain-model-taskgroup'
        ];
        foreach ($icons as $icon) {
            $iconRegistry->registerIcon(
                'tx-timelog-' . $icon,
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => 'EXT:timelog/Resources/Public/Icons/' . $icon . '.svg']
            );
        }
    }

    /**
     * Backend form data provider
     */
    if (1) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord']
        [\Buepro\Timelog\Backend\DataProvider\FormDataProvider::class] = [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexPrepare::class,
            ],
            'before' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\TcaFlexProcess::class,
            ],
        ];
    }

    /**
     * Hooks
     */
    if (1) {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']
            ['timelog'] = \Buepro\Timelog\Backend\Hook\DataHandlerHook::class;
    }
})();
