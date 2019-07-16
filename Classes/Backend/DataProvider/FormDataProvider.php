<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Backend\DataProvider;

use Buepro\Timelog\Utility\DatabaseUtility;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;

/**
 * Class FormDataProvider
 *
 * Sets the start time for an interval. Is used because the TCA definition is cached hence the current time
 * defined with `default = time()` is just applied upon clearing the cache.
 *
 */
class FormDataProvider implements FormDataProviderInterface
{
    /**
     * Adds an interval to a task by JS
     */
    private function addInterval()
    {
        $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Page\PageRenderer::class
        );
        $pageRenderer->loadRequireJsModule(
            'jquery',
            'function() { $(".inlineNewButton").trigger("click"); }'
        );
    }

    /**
     * Adds form data to result array
     *
     * @param array $result Initialized result array
     * @return array Result filled with more data
     * @throws \Exception
     * @see https://docs.typo3.org/typo3cms/CoreApiReference/8.7/ApiOverview/Database/Migration/Index.html#database-migration
     */
    public function addData(array $result)
    {
        // Adds data to interval
        if ($result['tableName'] === 'tx_timelog_domain_model_interval' && $result['command'] === 'new') {
            // Server time based on `phpTimeZone` defined in `LocalConfiguration.php`
            $serverTime = new \DateTime();

            // Sets start time
            if (isset($GLOBALS['BE_USER']->userTS['tx_timelog.']['timezone.']['offset'])) {
                // Takes into account userTS
                $daylightSaving = date('I');
                $offset = (int) $GLOBALS['BE_USER']->userTS['tx_timelog.']['timezone.']['offset'];
                $result['databaseRow']['start_time'] = $serverTime->getTimestamp();
                // Gets gmt+0 without daylight saving
                $result['databaseRow']['start_time'] -= $serverTime->getOffset();
                // Adds user gmt offset
                $result['databaseRow']['start_time'] += $offset * 3600;
                // Adds daylight saving
                $result['databaseRow']['start_time'] += $daylightSaving * 3600;
            } else {
                $result['databaseRow']['start_time'] = $serverTime->getTimestamp();
            }
        }

        // Adds data to task
        if ($result['tableName'] === 'tx_timelog_domain_model_task' && $result['command'] === 'new') {
            // Sets last used worker by be-user to task
            $latest = DatabaseUtility::getLatestRecord(
                'tx_timelog_domain_model_task',
                '*',
                sprintf('cruser_id = %d', $GLOBALS['BE_USER']->user['uid'])
            );
            if ($latest && $latest['worker']) {
                $result['databaseRow']['worker'] = $latest['worker'];
            }
            $this->addInterval();
        }
        return $result;
    }
}
