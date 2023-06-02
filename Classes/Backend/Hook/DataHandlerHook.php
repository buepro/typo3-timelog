<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Backend\Hook;

use Buepro\Timelog\Backend\Mediator\DataHandlerMediator;
use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Domain\Model\Task;
use Buepro\Timelog\Domain\Model\TaskGroup;
use Buepro\Timelog\Service\DatabaseService;
use Buepro\Timelog\Service\RegistryService;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Register changed object records at the DataHandlerMediator to get object states updated.
 */
class DataHandlerHook implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * Hook: processDatamap_preProcessFieldArray
     *
     * Initializes task data.
     *
     * @param array $incomingFieldArray
     * @param mixed|string $table
     * @param int|string $id
     * @param DataHandler $dataHandler
     * @throws \Exception
     */
    public function processDatamap_preProcessFieldArray(
        array &$incomingFieldArray,
        $table,
        $id,
        DataHandler $dataHandler
    ): void {
        if ($table === 'tx_timelog_domain_model_interval') {
            $this->reviewIntervalRecordData($incomingFieldArray);
        }
        if ($table === 'tx_timelog_domain_model_task') {
            $this->reviewTaskRecordData($incomingFieldArray);
            if (MathUtility::canBeInterpretedAsInteger($id)) {
                $this->checkTaskForRelationChange((int)$id, $incomingFieldArray);
            }
        }
    }

    /**
     * Register Intervals at the DataHandlerMediator for being updated.
     * Remark: Changed intervals lead to the tasks and the associated project and/or task group to be updated.
     *
     * When using the hook after INSERT operations, you will only get the temporary NEW... id passed to your hook as $id,
     * but you can easily translate it to the real uid of the inserted record using the $dataHandler->substNEWwithIDs array.
     *
     * @param string $status (reference) Status of the current operation, 'new' or 'update
     * @param int|string $table (reference) The table currently processing data for
     * @param int|string $id (reference) The record uid currently processing data for, [integer] or [string] (like 'NEW...')
     * @param array $fieldArray (reference) The field array of a record
     * @param DataHandler $dataHandler
     */
    public function processDatamap_afterDatabaseOperations(
        string $status,
        $table,
        $id,
        array $fieldArray,
        DataHandler $dataHandler
    ): void {
        if (
            $table === 'tx_timelog_domain_model_task' &&
            ($workerUid = (int) ($fieldArray['worker'] ?? 0)) !== 0
        ) {
            (new RegistryService())->setWorkerUidForBeUser($workerUid);
        }
        if (in_array($table, [
            'tx_timelog_domain_model_task',
            'tx_timelog_domain_model_interval',
            'tx_timelog_domain_model_project',
            'tx_timelog_domain_model_taskgroup'
        ], true)) {
            $uid = $id;
            if ($status === 'new') {
                $uid = $dataHandler->substNEWwithIDs[$id];
            }
            if ((int)$uid > 0) {
                $className = ucfirst(str_replace('tx_timelog_domain_model_', '', $table));
                if ($className === 'Taskgroup') {
                    $className = 'TaskGroup';
                }
                $className = 'Buepro\\Timelog\\Domain\\Model\\' . $className;
                GeneralUtility::makeInstance(DataHandlerMediator::class)
                    ->registerChangedObject($className, $uid);
            }
        }
    }

    protected function reviewTaskRecordData(array &$incomingFieldArray): void
    {
        if (isset($incomingFieldArray['active_time']) && $incomingFieldArray['active_time'] === '') {
            $incomingFieldArray['active_time'] = 0;
        }
        if (isset($incomingFieldArray['batch_date']) && $incomingFieldArray['batch_date'] === '') {
            $incomingFieldArray['batch_date'] = 0;
        }
    }

    protected function reviewIntervalRecordData(array &$incomingFieldArray): void
    {
        if (isset($incomingFieldArray['start_time']) && $incomingFieldArray['start_time'] === '') {
            $incomingFieldArray['start_time'] = 0;
        }
        if (isset($incomingFieldArray['end_time']) && $incomingFieldArray['end_time'] === '') {
            $incomingFieldArray['end_time'] = 0;
        }
        if (isset($incomingFieldArray['duration']) && $incomingFieldArray['duration'] === '') {
            $incomingFieldArray['duration'] = 0;
        }
    }

    protected function checkTaskForRelationChange(int $id, array &$incomingFieldArray): void
    {
        if (
            (
                $taskRecord = GeneralUtility::makeInstance(DatabaseService::class)
                ->getRecordByUid('tx_timelog_domain_model_task', $id, ['project', 'task_group', 'intervals'])
            ) !== null
        ) {
            $this->checkTaskForProjectChange($id, $incomingFieldArray, $taskRecord);
            $this->checkTaskForTaskGroupChange($id, $incomingFieldArray, $taskRecord);
            $this->checkTaskForIntervalChange($id, $incomingFieldArray, $taskRecord);
        }
    }

    protected function checkTaskForProjectChange(int $id, array &$incomingFieldArray, array $taskRecord): void
    {
        if (
            isset($incomingFieldArray['project']) &&
            (int)$incomingFieldArray['project'] !== (int)$taskRecord['project']
        ) {
            $mediator = GeneralUtility::makeInstance(DataHandlerMediator::class);
            // Register original project
            $mediator->registerChangedObject(Project::class, (int)$taskRecord['project']);
            // Register new project
            $mediator->registerChangedObject(Project::class, (int)$incomingFieldArray['project']);
            // Register possible, previous task group in case no project is assigned
            // Use case: Task group was assigned and the project assignment is now being changed.
            if (
                (int)$incomingFieldArray['project'] === 0 &&
                (isset($incomingFieldArray['task_group']) && (int)$incomingFieldArray['task_group'] > 0)
            ) {
                $incomingFieldArray['task_group'] = '';
                $mediator->registerChangedObject(TaskGroup::class, (int)$incomingFieldArray['task_group']);
            }
        }
    }

    protected function checkTaskForTaskGroupChange(int $id, array $incomingFieldArray, array $taskRecord): void
    {
        if (
            isset($incomingFieldArray['task_group']) &&
            (int)$incomingFieldArray['task_group'] !== (int)$taskRecord['task_group']
        ) {
            $this->registerTaskGroupAtMediator((int)$taskRecord['task_group']);
            $this->registerTaskGroupAtMediator((int)$incomingFieldArray['task_group']);
        }
    }

    protected function checkTaskForIntervalChange(int $id, array $incomingFieldArray, array $taskRecord): void
    {
        if (
            isset($incomingFieldArray['intervals']) &&
            ($exploded = GeneralUtility::trimExplode(',', $incomingFieldArray['intervals'], true)) !== [] &&
            count($exploded) !== (int)$taskRecord['intervals']
        ) {
            GeneralUtility::makeInstance(DataHandlerMediator::class)
                ->registerChangedObject(Task::class, $id);
        }
    }

    protected function registerTaskGroupAtMediator(int $taskGroupUid): void
    {
        $mediator = GeneralUtility::makeInstance(DataHandlerMediator::class);
        $databaseService = GeneralUtility::makeInstance(DatabaseService::class);
        $mediator->registerChangedObject(TaskGroup::class, $taskGroupUid);
        $taskGroupRecord = $databaseService->getRecordByUid(
            'tx_timelog_domain_model_taskgroup',
            $taskGroupUid,
            ['project']
        );
        if ($taskGroupRecord !== null && (int)$taskGroupRecord['project'] > 0) {
            $mediator->registerChangedObject(Project::class, (int)$taskGroupRecord['project']);
        }
    }
}
