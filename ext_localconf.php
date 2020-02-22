<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function () {
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

        // wizards
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    taskpanel {
                        iconIdentifier = timelog-plugin-taskpanel
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
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);

        $iconRegistry->registerIcon(
            'timelog-plugin-taskpanel',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:timelog/Resources/Public/Icons/user_plugin_taskpanel.svg']
        );
    }
);

//# EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

call_user_func(
    function () {
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
                'tx_timelog_domain_model_project',
                'tx_timelog_domain_model_task',
                'tx_timelog_domain_model_interval'
            ];
            foreach ($icons as $icon) {
                $iconRegistry->registerIcon(
                    $icon,
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

        /**
         * Signals
         */
        if (1) {
            $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
            );

            // Extbase object persisted
            $signalSlotDispatcher->connect(
                \TYPO3\CMS\Extbase\Persistence\Generic\Backend::class,
                'afterPersistObject',
                \Buepro\Timelog\Mediator\HandleMediator::class,
                'handleAfterPersistObject'
            );

            // Task active time change
            $signalSlotDispatcher->connect(
                \Buepro\Timelog\Domain\Model\Task::class,
                'activeTimeChange',
                \Buepro\Timelog\Mediator\TimeMediator::class,
                'handleTaskActiveTimeChange'
            );

            // Task batch date change
            $signalSlotDispatcher->connect(
                \Buepro\Timelog\Domain\Model\Task::class,
                'batchDateChange',
                \Buepro\Timelog\Mediator\TimeMediator::class,
                'handleTaskBatchDateChange'
            );
        }
    }
);
