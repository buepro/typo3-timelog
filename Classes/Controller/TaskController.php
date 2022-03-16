<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Controller;

use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Domain\Model\Task;
use Buepro\Timelog\Domain\Model\TaskGroup;
use Buepro\Timelog\Domain\Repository\ProjectRepository;
use Buepro\Timelog\Domain\Repository\TaskGroupRepository;
use Buepro\Timelog\Domain\Repository\TaskRepository;
use Buepro\Timelog\Utility\GeneralUtility;
use DateTime;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * TaskController
 */
class TaskController extends ActionController
{

    /**
     * projectRepository
     *
     * @var ProjectRepository
     */
    protected $projectRepository = null;

    /**
     * taskGroupRepository
     *
     * @var TaskGroupRepository
     */
    protected $taskGroupRepository = null;

    /**
     * taskRepository
     *
     * @var TaskRepository
     */
    protected $taskRepository = null;

    /**
     * Plugin ts setup
     * @var array
     */
    private $tsSetup = [];

    /**
     * TaskController constructor.
     *
     * @param ProjectRepository $projectRepository
     * @param TaskGroupRepository $taskGroupRepository
     * @param TaskRepository $taskRepository
     */
    public function __construct(
        ProjectRepository $projectRepository,
        TaskGroupRepository $taskGroupRepository,
        TaskRepository $taskRepository
    ) {
        $this->projectRepository = $projectRepository;
        $this->taskGroupRepository = $taskGroupRepository;
        $this->taskRepository = $taskRepository;
    }

    /**
     * Gets models for filter according description from listAction().
     * The models consist of project, tasks, task group, task groups, batch and batches.
     *
     * @param string $projectHandle
     * @param string $taskGroupHandle With `exclude` just tasks not belonging to a task group are obtained
     * @param string $batchHandle
     * @param string $taskHandle
     * @return array
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    private function getModelsForFilter(
        string $projectHandle = '',
        string $taskGroupHandle = '',
        string $batchHandle = '',
        string $taskHandle = ''
    ) {
        $emptyModels = [
            'project' => null,
            'tasks' => null,
            'taskGroup' => null,
            'taskGroups' => null,
            'batch' => null,
            'batches' => null
        ];

        // Return empty result in case no handle is provided
        $handles = implode('', func_get_args());
        if ($handles === '' || $handles === 'exclude') {
            return $emptyModels;
        }

        /** @var ?Project $project */
        $project = null;
        /** @var ?Task $task */
        $task = null;
        /** @var ?TaskGroup $taskGroup */
        $taskGroup = null;
        $taskGroups = null;
        $batch = null;
        $batches = null;
        $batchTime = 0;

        // Obtain models directly defined by handles
        if ($taskHandle !== '') {
            // @phpstan-ignore-next-line
            $tasks = $this->taskRepository->findByHandle($taskHandle);
            if ($tasks instanceof QueryResultInterface && (bool)$tasks->count()) {
                $task = $tasks->getFirst();
                if (!($task instanceof Task)) {
                    $task = null;
                }
            }
        }
        if ($batchHandle !== '') {
            $decodedBatchHandle = GeneralUtility::decodeBatchHandle($batchHandle);
            if ($decodedBatchHandle !== null && (bool)$decodedBatchHandle['taskUid']) {
                $task = $this->taskRepository->findByUid((int)$decodedBatchHandle['taskUid']);
                if (!($task instanceof Task)) {
                    $task = null;
                }
            }
        }
        if ($taskGroupHandle !== '' && $taskGroupHandle !== 'exclude') {
            // @phpstan-ignore-next-line
            $taskGroup = $this->taskGroupRepository->findByHandle($taskGroupHandle)->getFirst();
            if (!($taskGroup instanceof TaskGroup)) {
                $taskGroup = null;
            }
        }
        if ($projectHandle !== '') {
            // @phpstan-ignore-next-line
            $project = $this->projectRepository->findByHandle($projectHandle)->getFirst();
            if (!($project instanceof Project)) {
                $project = null;
            }
        }

        // Obtain dependent models and data
        if ($task !== null) {
            if ($project === null) {
                $project = $task->getProject();
            }
            if ($taskGroup === null) {
                $taskGroup = $task->getTaskGroup();
            }
        }
        if ($project === null && $taskGroup !== null && $taskGroup->getProject() !== null) {
            $project = $taskGroup->getProject();
        }
        if ($project !== null) {
            $taskGroups = $project->getTaskGroups();
        }
        if ($taskGroup !== null) {
            $batches = $this->taskRepository->getBatches($taskGroup);
        } elseif ($project !== null) {
            $batches = $this->taskRepository->getBatches($project, !($taskGroupHandle === 'exclude'));
        }
        if ($batches !== null && $task !== null && $task->getBatchDate() !== null) {
            $timestamp = $task->getBatchDate()->getTimestamp();
            $taskGroupUid = 0;
            if ($task->getTaskGroup() !== null) {
                $taskGroupUid = $task->getTaskGroup()->getUid();
            }
            foreach ($batches as $aBatch) {
                if ($aBatch['timestamp'] === $timestamp && $aBatch['taskGroup'] === $taskGroupUid) {
                    $batch = $aBatch;
                }
            }
            if ($batch) {
                $batchTime = $batch['timestamp'];
            }
        }
        if ($task !== null && ((bool)$project || (bool)$taskGroup || (bool)$batch)) {
            $task = null;
        }

        // Manage task group exclude function
        $taskGroupFilter = null;
        if ($taskGroup instanceof TaskGroup) {
            $taskGroupFilter = $taskGroup;
        } elseif ($taskGroupHandle === 'exclude') {
            $taskGroupFilter = 0;
        }

        // Get tasks
        $tasks = $this->taskRepository->findForFilter(
            $project,
            $task,
            $taskGroupFilter,
            $batchTime
        );

        return [
            'project' => $project,
            'tasks' => $tasks,
            'taskGroup' => $taskGroup,
            'taskGroups' => $taskGroups,
            'batch' => $batch,
            'batches' => $batches
        ];
    }

    /**
     * Gets tasks according the requested project, task group and batch handle.
     * In case no batch is defined heap tasks will be gathered.
     * In case a task handle is provided the project, task group and batch are received by the task and possible
     * provided handles (for project, task group and batch) are ignored.
     *
     * @param string $projectHandle Optional project handle
     * @param string $taskGroupHandle Optional task group handle
     * @param string $batchHandle Optional batch handle
     * @param string $taskHandle Optional task handle
     * @return void
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     * @throws \Exception
     */
    public function listAction(
        string $projectHandle = '',
        string $taskGroupHandle = '',
        string $batchHandle = '',
        string $taskHandle = ''
    ) {
        $models = $this->getModelsForFilter($projectHandle, $taskGroupHandle, $batchHandle, $taskHandle);
        [
            'project' => $project,
            'tasks' => $tasks,
            'taskGroup' => $taskGroup,
            'taskGroups' => $taskGroups,
            'batch' => $batch,
            'batches' => $batches
        ] = $models;

        $tasksCount = 0;
        $hasTasksOnHeap = false;
        if ($tasks instanceof QueryResultInterface && (bool)$tasks->count()) {
            $tasksCount = $tasks->count();
            foreach ($tasks as $aTask) {
                if (!$aTask->getBatchDate()) {
                    $hasTasksOnHeap = true;
                    break;
                }
            }
        }

        $this->view->assignMultiple([
            'projectHandle' => $project ? $project->getHandle() : '',
            'project' => $project,
            'tasks' => $tasks,
            'taskGroup' => $taskGroup,
            'taskGroupExclude' => !$taskGroup && $taskGroupHandle === 'exclude',
            'taskGroups' => $taskGroups,
            'batch' => $batch,
            'batches' => $batches,
            'noTasksFound' =>
                ((bool)$projectHandle || (bool)$taskGroupHandle || (bool)$batchHandle || (bool)$taskHandle) &&
                $tasksCount === 0,
            'hasTasksOnHeap' => $hasTasksOnHeap,
            'settings' => $this->settings,
        ]);
    }

    /**
     * Creates a task batch from the task heap
     */
    public function createBatchAction(string $projectHandle = '', string $taskGroupHandle = ''): void
    {
        $models = $this->getModelsForFilter($projectHandle, $taskGroupHandle);
        if (!$models['tasks'] instanceof QueryResultInterface || $models['tasks']->count() < 1) {
            // @extensionScannerIgnoreLine
            $this->redirect('list', null, null, ['projectHandle' => $projectHandle]);
        }
        $tasks = $models['tasks'];
        $batchDate = new DateTime('now');
        foreach ($tasks as $task) {
            $task->setBatchDate($batchDate);
            $this->taskRepository->update($task);
        }
//        $this->projectRepository->update($project);
        $batchHandle = GeneralUtility::getBatchHandle(
            $batchDate->getTimestamp(),
            $tasks->getFirst() !== null ? $tasks->getFirst()->getUid() : 0
        );
        // @extensionScannerIgnoreLine
        $this->redirect('list', null, null, ['batchHandle' => $batchHandle]);
    }

    public function errorAction(): string
    {
        if (!isset($this->tsSetup['persistence.']['storagePid']) || !$this->tsSetup['persistence.']['storagePid']) {
            // @extensionScannerIgnoreLine
            $this->addFlashMessage(
                'The storagePid isn\'t defined. Please review the "Record Storage Page" field and the TS constants.',
                'Configuration missing',
                AbstractMessage::ERROR,
                true
            );
        }
        return parent::errorAction();
    }

    /**
     * Checks precondition.
     *
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     */
    protected function initializeAction(): void
    {
        // Forwards to errorAction
        if ($this->actionMethodName === 'errorAction') {
            return;
        }

        // Checks configuration
        $configuration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
        if (
            !isset($configuration['persistence']['storagePid']) ||
            (int)$configuration['persistence']['storagePid'] < 1
        ) {
            // @extensionScannerIgnoreLine
            $this->redirect('error');
        }
    }

    /**
     * Initializes the view before invoking an action method.
     *
     * @param ViewInterface $view The view to be initialized
     */
    protected function initializeView(ViewInterface $view): void
    {
        $view->assign('controller', 'Task');
    }
}
