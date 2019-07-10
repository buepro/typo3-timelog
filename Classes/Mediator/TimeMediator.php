<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Mediator;

use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Domain\Model\Task;
use Buepro\Timelog\Domain\Repository\ProjectRepository;
use Buepro\Timelog\Domain\Repository\TaskRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Coordinates everything related to task and project time.
 *
 */
class TimeMediator implements SingletonInterface
{
    /**
     * @var ProjectRepository
     */
    protected $projectRepository;

    /**
     * @var TaskRepository
     */
    protected $taskRepository;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->projectRepository = $this->objectManager->get(ProjectRepository::class);
        $this->taskRepository = $this->objectManager->get(TaskRepository::class);
    }

    /**
     * Calculates the active, heap and batch time from a project
     *
     * @param Project $project
     * @return array Contains the keys active, heap and stack
     */
    private function calcProjectTime(Project $project)
    {
        $result = [
            'active' => 0.0,
            'heap' => 0.0,
            'batch' => 0.0,
        ];
        $tasks = $this->taskRepository->findByProject($project);
        foreach ($tasks as $task) {
            $result['active'] += $task->getActiveTime();
            if (!$task->getBatchDate() || $task->getBatchDate()->getTimestamp() === 0) {
                $result['heap'] += $task->getActiveTime();
            }
        }
        $result['batch'] = $result['active'] - $result['heap'];
        return $result;
    }

    /**
     * Updates the active, batch and heap time from a project.
     *
     * @param Project | null $project
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    private function updateProjectTime($project)
    {
        if ($project) {
            $projectTime = $this->calcProjectTime($project);
            $project->setActiveTime($projectTime['active']);
            $project->setHeapTime($projectTime['heap']);
            $project->setBatchTime($projectTime['batch']);
            $this->projectRepository->update($project);
        }
    }

    /**
     * Handles the activTimeChange signal from a task to update the active, batch and heap time for the associated
     * project.
     *
     * @param Task $task
     * @param float $previousActiveTime
     * @param float $newActiveTime
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function handleTaskActiveTimeChange(Task $task, $previousActiveTime, $newActiveTime)
    {
        $this->updateProjectTime($task->getProject());
    }

    public function handleTaskBatchDateChange(Task $task, $previousBatchDate, $currentBatchDate)
    {
        $this->updateProjectTime($task->getProject());
    }
}
