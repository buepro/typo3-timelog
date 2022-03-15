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

        /**
         * @var Project $project
         * @var Task $task
         * @var TaskGroup $taskGroup
         */
        $project = null;
        $task = null;
        $taskGroup = null;
        $taskGroups = null;
        $batch = null;
        $batches = null;
        $batchTime = 0;

        // Obtain models directly defined by handles
        if ($taskHandle) {
            $tasks = $this->taskRepository->findByHandle($taskHandle);
            if ($tasks instanceof QueryResultInterface && $tasks->count()) {
                $task = $tasks->getFirst();
                if (!($task instanceof Task)) {
                    $task = null;
                }
            }
        }
        if ($batchHandle) {
            $decodedBatchHandle = GeneralUtility::decodeBatchHandle($batchHandle);
            if ($decodedBatchHandle && (int)$decodedBatchHandle['taskUid']) {
                $task = $this->taskRepository->findByUid((int)$decodedBatchHandle['taskUid']);
                if (!($task instanceof Task)) {
                    $task = null;
                }
            }
        }
        if ($taskGroupHandle && $taskGroupHandle !== 'exclude') {
            $taskGroup = $this->taskGroupRepository->findByHandle($taskGroupHandle)->getFirst();
            if (!($taskGroup instanceof TaskGroup)) {
                $taskGroup = null;
            }
        }
        if ($projectHandle) {
            $project = $this->projectRepository->findByHandle($projectHandle)->getFirst();
            if (!($project instanceof Project)) {
                $project = null;
            }
        }

        // Obtain dependent models and data
        if ($task) {
            if (!$project) {
                $project = $task->getProject();
            }
            if (!$taskGroup) {
                $taskGroup = $task->getTaskGroup();
            }
        }
        if (!$project && $taskGroup && $taskGroup->getProject()) {
            $project = $taskGroup->getProject();
        }
        if ($project) {
            $taskGroups = $project->getTaskGroups();
        }
        if ($taskGroup) {
            $batches = $this->taskRepository->getBatches($taskGroup);
        } elseif ($project) {
            $batches = $this->taskRepository->getBatches($project, !($taskGroupHandle === 'exclude'));
        }
        if ($batches && $task && $task->getBatchDate()) {
            $timestamp = $task->getBatchDate()->getTimestamp();
            $taskGroupUid = 0;
            if ($task->getTaskGroup()) {
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
        if ($task && ($project || $taskGroup || $batch)) {
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
        if ($tasks instanceof QueryResultInterface && $tasks->count()) {
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
            'noTasksFound' => ($projectHandle || $taskGroupHandle || $batchHandle || $taskHandle) && !$tasksCount,
            'hasTasksOnHeap' => $hasTasksOnHeap,
            'settings' => $this->settings,
        ]);
    }

    /**
     * Creates a task batch from the task heap
     *
     * @param string $projectHandle
     * @param string $taskGroupHandle
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws UnknownObjectException
     */
    public function createBatchAction(string $projectHandle = '', string $taskGroupHandle = '')
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
            $tasks->getFirst()->getUid()
        );
        // @extensionScannerIgnoreLine
        $this->redirect('list', null, null, ['batchHandle' => $batchHandle]);
    }

    public function errorAction()
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
    }

    /**
     * Checks precondition.
     *
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     */
    protected function initializeAction()
    {
        // Forwards to errorAction
        if ($this->actionMethodName === 'errorAction') {
            return;
        }

        // Checks configuration
        $configuration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
        if (empty($configuration['persistence']['storagePid'])) {
            // @extensionScannerIgnoreLine
            $this->redirect('error');
        }
    }

    /**
     * Initializes the view before invoking an action method.
     *
     * @param ViewInterface $view The view to be initialized
     */
    protected function initializeView(ViewInterface $view)
    {
        $view->assign('controller', 'Task');
    }
}
