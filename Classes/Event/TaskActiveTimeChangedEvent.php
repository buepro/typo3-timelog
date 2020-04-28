<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Event;

use Buepro\Timelog\Domain\Model\Task;

/**
 * Event to be ired when an active time changed.
 *
 */
final class TaskActiveTimeChangedEvent
{
    /**
     * @var Task
     */
    private $task;

    /**
     * @var float
     */
    private $previousActiveTime;

    /**
     * @var float
     */
    private $newActiveTime;

    public function __construct(Task $task, float $previousActiveTime, float $newActiveTime)
    {
        $this->task = $task;
        $this->previousActiveTime = $previousActiveTime;
        $this->newActiveTime = $newActiveTime;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getPreviousActiveTime(): float
    {
        return $this->previousActiveTime;
    }

    public function getNewActiveTime(): float
    {
        return $this->newActiveTime;
    }
}
