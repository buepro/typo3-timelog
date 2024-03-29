<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Backend\DataProvider;

use Buepro\Timelog\Service\RegistryService;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
    private function addInterval(): void
    {
        $pageRenderer = GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Page\PageRenderer::class
        );
        $pageRenderer->loadJavaScriptModule('@buepro/timelog/Task.js');
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
        // Add data to interval
        if ($result['tableName'] === 'tx_timelog_domain_model_interval' && $result['command'] === 'new') {
            // Server time based on `phpTimeZone` defined in `LocalConfiguration.php`
            $serverTime = new \DateTime();

            // Set start time
            $tsConfig = $GLOBALS['BE_USER']->getTSConfig();
            if (isset($tsConfig['tx_timelog.']['timezone.']['offset'])) {
                // Take into account userTS
                $daylightSaving = date('I');
                $offset = (int) $tsConfig['tx_timelog.']['timezone.']['offset'];
                $result['databaseRow']['start_time'] = $serverTime->getTimestamp();
                // Get gmt+0 without daylight saving
                $result['databaseRow']['start_time'] -= $serverTime->getOffset();
                // Add user gmt offset
                $result['databaseRow']['start_time'] += $offset * 3600;
                // Add daylight saving
                $result['databaseRow']['start_time'] += $daylightSaving * 3600;
            } else {
                $result['databaseRow']['start_time'] = $serverTime->getTimestamp();
            }
        }

        // Add data to task
        if ($result['tableName'] === 'tx_timelog_domain_model_task' && $result['command'] === 'new') {
            if (($workerUid = (new RegistryService())->getWorkerUidForBeUser()) > 0) {
                $result['databaseRow']['worker'] = $workerUid;
            }
            $this->addInterval();
        }

        return $result;
    }
}
