<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Mediator;

use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Domain\Model\Task;
use Buepro\Timelog\Domain\Model\TaskGroup;
use Buepro\Timelog\Domain\Repository\ProjectRepository;
use Buepro\Timelog\Domain\Repository\TaskGroupRepository;
use Buepro\Timelog\Domain\Repository\TaskRepository;
use Buepro\Timelog\Event\TaskActiveTimeChangedEvent;
use Buepro\Timelog\Event\TaskBatchDateChangedEvent;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

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
     * @var TaskGroupRepository
     */
    protected $taskGroupRepository;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * TimeMediator constructor.
     *
     * @param ProjectRepository $projectRepository
     * @param TaskRepository $taskRepository
     * @param TaskGroupRepository $taskGroupRepository
     */
    public function __construct(
        ProjectRepository $projectRepository,
        TaskRepository $taskRepository,
        TaskGroupRepository $taskGroupRepository
    ) {
        $this->projectRepository = $projectRepository;
        $this->taskRepository = $taskRepository;
        $this->taskGroupRepository = $taskGroupRepository;
    }

    /**
     * Calculates the active, heap and batch time from tasks
     *
     * @param QueryResultInterface $tasks
     * @return array Contains the keys active, heap and stack
     */
    private function calcTimeForTasks(QueryResultInterface $tasks)
    {
        $result = [
            'active' => 0.0,
            'heap' => 0.0,
            'batch' => 0.0,
        ];
        /** @var Task $task */
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
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    private function updateProjectTime($project)
    {
        if ($project) {
            $tasks = $this->taskRepository->findByProject($project);
            $projectTime = $this->calcTimeForTasks($tasks);
            $project->setActiveTime($projectTime['active']);
            $project->setHeapTime($projectTime['heap']);
            $project->setBatchTime($projectTime['batch']);
            $this->projectRepository->update($project);
        }
    }

    private function updateTaskGroupTime(TaskGroup $taskGroup)
    {
        if ($taskGroup) {
            $tasks = $this->taskRepository->findByTaskGroup($taskGroup);
            $taskGroupTime = $this->calcTimeForTasks($tasks);
            $taskGroup->setActiveTime($taskGroupTime['active']);
            $taskGroup->setHeapTime($taskGroupTime['heap']);
            $taskGroup->setBatchTime($taskGroupTime['batch']);
            $this->taskGroupRepository->update($taskGroup);
        }
    }

    /**
     * Handles the event `TaskActiveTimeChangedEvent` from a task to update the active, batch and heap time for the
     * associated project.
     *
     * @param TaskActiveTimeChangedEvent $event
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function handleTaskActiveTimeChangedEvent(TaskActiveTimeChangedEvent $event)
    {
        if ($event->getTask()->getProject()) {
            $this->updateProjectTime($event->getTask()->getProject());
        }
        if ($event->getTask()->getTaskGroup()) {
            $this->updateTaskGroupTime($event->getTask()->getTaskGroup());
        }
    }

    /**
     * @param TaskBatchDateChangedEvent $event
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function handleTaskBatchDateChangedEvent(TaskBatchDateChangedEvent $event)
    {
        if ($event->getTask()->getProject()) {
            $this->updateProjectTime($event->getTask()->getProject());
        }
        if ($event->getTask()->getTaskGroup()) {
            $this->updateTaskGroupTime($event->getTask()->getTaskGroup());
        }
    }
}
