<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Event;

use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Domain\Model\Task;

final class TaskProjectChangeEvent
{
    /** @var Task */
    protected $task;
    /** @var ?Project $previousProject */
    protected $previousProject;
    /** @var ?Project $project */
    protected $project;

    public function __construct(Task $task, ?Project $previousProject, ?Project $project)
    {
        $this->task = $task;
        $this->previousProject = $previousProject;
        $this->project = $project;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getPreviousProject(): ?Project
    {
        return $this->previousProject;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }
}
