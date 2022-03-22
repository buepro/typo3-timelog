<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Event;

use Buepro\Timelog\Domain\Model\Task;
use Buepro\Timelog\Domain\Model\TaskGroup;

final class TaskTaskGroupChangeEvent
{
    /** @var Task */
    protected $task;
    /** @var ?TaskGroup $previousTaskGroup */
    protected $previousTaskGroup;
    /** @var ?TaskGroup $taskGroup */
    protected $taskGroup;

    public function __construct(Task $task, ?TaskGroup $previousTaskGroup, ?TaskGroup $taskGroup)
    {
        $this->task = $task;
        $this->previousTaskGroup = $previousTaskGroup;
        $this->taskGroup = $taskGroup;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getPreviousTaskGroup(): ?TaskGroup
    {
        return $this->previousTaskGroup;
    }

    public function getTaskGroup(): ?TaskGroup
    {
        return $this->taskGroup;
    }
}
