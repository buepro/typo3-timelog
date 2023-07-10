<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3') || die('Access denied.');

(function () {
    /**
     * Configure plugin
     */
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
