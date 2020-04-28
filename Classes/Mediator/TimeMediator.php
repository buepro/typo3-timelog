<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Mediator;

use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Domain\Repository\ProjectRepository;
use Buepro\Timelog\Domain\Repository\TaskRepository;
use Buepro\Timelog\Event\TaskActiveTimeChangedEvent;
use Buepro\Timelog\Event\TaskBatchDateChangedEvent;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;

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

    /**
     * TimeMediator constructor.
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
     * Calculates the active, heap and batch time from a project
     *
     * @param Project $project
     * @return array Contains the keys active, heap and stack
     * @throws \TYPO3\CMS\Extbase\Object\Exception
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
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Object\Exception
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
        $this->updateProjectTime($event->getTask()->getProject());
    }

    /**
     * @param TaskBatchDateChangedEvent $event
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function handleTaskBatchDateChangedEvent(TaskBatchDateChangedEvent $event)
    {
        $this->updateProjectTime($event->getTask()->getProject());
    }
}
