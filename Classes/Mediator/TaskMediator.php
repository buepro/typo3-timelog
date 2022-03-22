<?php

declare(strict_types=1);

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
use Buepro\Timelog\Event\TaskProjectChangeEvent;
use Buepro\Timelog\Event\TaskTaskGroupChangeEvent;
use Buepro\Timelog\Event\TaskTimeChangeEvent;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Coordinate everything related to task time.
 */
class TaskMediator implements SingletonInterface
{
    /** @var TaskRepository */
    protected $taskRepository;

    /** @var ProjectRepository */
    protected $projectRepository;

    /** @var TaskGroupRepository */
    protected $taskGroupRepository;

    public function __construct(
        TaskRepository $taskRepository,
        ProjectRepository $projectRepository,
        TaskGroupRepository $taskGroupRepository
    ) {
        $this->projectRepository = $projectRepository;
        $this->taskRepository = $taskRepository;
        $this->taskGroupRepository = $taskGroupRepository;
    }

    /**
     * Update the active, batch and heap time for the associated project and task group.
     */
    public function handleTimeChangeEvent(TaskTimeChangeEvent $event): void
    {
        $this->updateTaskChildren($event->getTask());
    }

    public function handleTaskGroupChangeEvent(TaskTaskGroupChangeEvent $event): void
    {
        $this->updateTaskGroup($event->getPreviousTaskGroup());
        $this->updateTaskGroup($event->getTaskGroup());
    }

    public function handleProjectChangeEvent(TaskProjectChangeEvent $event): void
    {
        $this->updateProject($event->getPreviousProject());
        $this->updateProject($event->getProject());
    }

    protected function updateTaskChildren(Task $task): void
    {
        if (($project = $task->getProject()) !== null) {
            $project->update();
            $this->projectRepository->update($project);
        }
        if (($taskGroup = $task->getTaskGroup()) !== null) {
            $taskGroup->update();
            $this->taskGroupRepository->update($taskGroup);
        }
    }

    protected function updateTaskGroup(?TaskGroup $taskGroup): void
    {
        if ($taskGroup === null) {
            return;
        }
        $taskGroup->update();
        $this->taskGroupRepository->update($taskGroup);
        if (($project = $taskGroup->getProject()) !== null) {
            $project->update();
            $this->projectRepository->update($project);
        }
    }

    protected function updateProject(?Project $project): void
    {
        if ($project === null) {
            return;
        }
        $project->update();
        $this->projectRepository->update($project);
    }
}
