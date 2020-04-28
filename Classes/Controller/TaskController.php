<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Controller;

use Buepro\Timelog\Domain\Model\Task;
use Buepro\Timelog\Domain\Repository\ProjectRepository;
use Buepro\Timelog\Domain\Repository\TaskRepository;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/***
 *
 * This file is part of the "Timelog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Roman Büchler <rb@buechler.pro>, buechler.pro
 *
 ***/
/**
 * TaskController
 */
class TaskController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * projectRepository
     *
     * @var \Buepro\Timelog\Domain\Repository\ProjectRepository
     */
    protected $projectRepository = null;

    /**
     * taskRepository
     *
     * @var \Buepro\Timelog\Domain\Repository\TaskRepository
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
     * @param TaskRepository $taskRepository
     */
    public function __construct(ProjectRepository $projectRepository, TaskRepository $taskRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->taskRepository = $taskRepository;
    }

    /**
     * Checks precondition.
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    protected function initializeAction()
    {
        // Forwards to errorAction
        if ($this->actionMethodName === 'errorAction') {
            return;
        }

        // Checks configuration
        $configuration = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
        if (empty($configuration['persistence']['storagePid'])) {
            /** @extensionScannerIgnoreLine */
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

    /**
     * Lists tasks and gets batches for a project. Three use cases are distinguished:
     *
     * 1. Project handle is provided
     * The heap tasks for the project are listed.
     *
     * 2. Batch handle is provided
     * The batch tasks for the project are listed.
     *
     * 3. Task handle is provided
     * In case a project is assigned to the related task the heap or batch tasks are shown. Otherwise just the task
     * will be shown.
     *
     * @param string $projectHandle Optional project handle
     * @param string $batchHandle Optional batch handle
     * @param string $taskHandle Optional task handle
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \Exception
     */
    public function listAction(string $projectHandle = '', string $batchHandle = '', string $taskHandle = '')
    {
        $project = null;
        $tasks = null;

        // Use case 3: Gets $batchHandle and $projectHandle for $taskHandle
        if ($taskHandle) {
            $task = $this->taskRepository->findByHandle($taskHandle)->getFirst();
            if ($task) {
                // In case no project is assigned just the task is returned
                $tasks = [$task];
                // Gets the batch handle
                if ($task->getBatchDate()) {
                    $batchHandle = \Buepro\Timelog\Utility\GeneralUtility::getBatchHandle(
                        $task->getBatchDate()->getTimestamp(),
                        $task->getUid()
                    );
                }
                // Gets the project handle
                $project = $task->getProject();
                if ($project) {
                    $projectHandle = $project->getHandle();
                }
            }
        }

        // Use cases 1. and 2.: Gets project and/or tasks
        if ($batchHandle) {
            $tasks = $this->taskRepository->findBatchTasks($batchHandle);
            if ($tasks) {
                $project = $tasks[0]->getProject();
            }
        } elseif ($projectHandle) {
            if (!$project) {
                $project = $this->projectRepository->findByHandle($projectHandle)->getFirst();
            }
            if ($project) {
                $tasks = $this->taskRepository->findHeapTasks($project);
            }
        }

        // Gets batches and batch
        $batches = $this->taskRepository->getBatches($project);
        $batch = [];
        ['timestamp' => $batchTimestamp] = \Buepro\Timelog\Utility\GeneralUtility::decodeBatchHandle($batchHandle);
        foreach ($batches as $abatch) {
            if ($abatch['timestamp'] === $batchTimestamp) {
                $batch = $abatch;
            }
        }

        $this->view->assignMultiple([
            'projectHandle' => $project ? $project->getHandle() : $taskHandle,
            'project' => $project,
            'tasks' => $tasks,
            'batches' => $batches,
            'batch' => $batch,
            'noTasksFound' => ($projectHandle || $batchHandle || $taskHandle) && !$tasks,
            'settings' => $this->settings,
        ]);
    }

    /**
     * Creates a task batch from the task heap
     *
     * @param string $projectHandle
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     */
    public function createBatchAction(string $projectHandle = '')
    {
        $project = $this->projectRepository->findByHandle($projectHandle)->getFirst();
        if (!$project) {
            /** @extensionScannerIgnoreLine */
            $this->redirect('list');
        }
        $tasks = $this->taskRepository->findHeapTasks($project);
        if (!$tasks) {
            /** @extensionScannerIgnoreLine */
            $this->redirect('list', null, null, ['projectHandle' => $projectHandle]);
        }
        $batchDate = new \DateTime('now');
        foreach ($tasks as $task) {
            $task->setBatchDate($batchDate);
            $this->taskRepository->update($task);
        }
        $this->projectRepository->update($project);
        $batchHandle = \Buepro\Timelog\Utility\GeneralUtility::getBatchHandle(
            $batchDate->getTimestamp(),
            $tasks[0]->getUid()
        );
        /** @extensionScannerIgnoreLine */
        $this->redirect('list', null, null, ['batchHandle' => $batchHandle]);
    }

    public function errorAction()
    {
        if (!isset($this->tsSetup['persistence.']['storagePid']) || !$this->tsSetup['persistence.']['storagePid']) {
            /** @extensionScannerIgnoreLine */
            $this->addFlashMessage(
                'The storagePid isn\'t defined. Please review the "Record Storage Page" field and the TS constants.',
                'Configuration missing',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR,
                true
            );
        }
    }
}
