<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Backend\Hook;

/**
 * Created by PhpStorm.
 * User: roman
 * Date: 20/02/2019
 * Time: 12:04
 */
use Buepro\Timelog\Backend\Mediator\CoreMediator;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TaskDataHandlerHook
 *
 * Initializes the task and project data and registers the related task or project at the CoreMediator to be updated.
 *
 */
class DataHandlerHook implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * Hook: processDatamap_preProcessFieldArray
     *
     * Initializes task data.
     *
     * @param $incomingFieldArray
     * @param $table
     * @param $id
     * @param $dataHandler
     * @throws \Exception
     */
    public function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, $dataHandler)
    {
        // Reviews task data
        if ($table === 'tx_timelog_domain_model_task') {
            if ($incomingFieldArray['active_time'] === '') {
                $incomingFieldArray['active_time'] = 0;
            }
            if ($incomingFieldArray['batch_date'] === '') {
                $incomingFieldArray['batch_date'] = 0;
            }
        }

        // Reviews interval data
        if ($table === 'tx_timelog_domain_model_interval') {
            if ($incomingFieldArray['start_time'] === '') {
                $incomingFieldArray['start_time'] = 0;
            }
            if ($incomingFieldArray['end_time'] === '') {
                $incomingFieldArray['end_time'] = 0;
            }
            if ($incomingFieldArray['duration'] === '') {
                $incomingFieldArray['duration'] = 0;
            }
        }
    }

    /**
     * Hook: processDatamap_afterDatabaseOperations
     *
     * Registers a Project, Task or TaskGroup at the CoreMediator for being updated.
     *
     * When using the hook after INSERT operations, you will only get the temporary NEW... id passed to your hook as $id,
     * but you can easily translate it to the real uid of the inserted record using the $dataHandler->substNEWwithIDs array.
     *
     * @param string $status (reference) Status of the current operation, 'new' or 'update
     * @param string $table (reference) The table currently processing data for
     * @param string $id (reference) The record uid currently processing data for, [integer] or [string] (like 'NEW...')
     * @param array $fieldArray (reference) The field array of a record
     * @param $dataHandler
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $dataHandler)
    {
        if (in_array($table, [
            'tx_timelog_domain_model_project',
            'tx_timelog_domain_model_task',
            'tx_timelog_domain_model_taskgroup'
        ], true)) {
            $uid = $id;
            if ($status === 'new') {
                $uid = $dataHandler->substNEWwithIDs[$id];
            }
            if ($uid) {
                $className = ucfirst(str_replace('tx_timelog_domain_model_', '', $table));
                if ($className === 'Taskgroup') {
                    $className = 'TaskGroup';
                }
                $className = 'Buepro\\Timelog\\Domain\\Model\\' . $className;
                GeneralUtility::makeInstance(CoreMediator::class)
                    ->registerChangedObjectUid($className, $uid);
            }
        }
    }
}
