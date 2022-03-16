<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Event;

use Buepro\Timelog\Domain\Model\Task;
use DateTime;

/**
 * Event to be triggered when a task batch date changes
 *
 */
final class TaskBatchDateChangedEvent
{
    /**
     * @var Task
     */
    private $task;

    /**
     * @var DateTime|null
     */
    private $previousDate;

    /**
     * @var DateTime|null
     */
    private $currentDate;

    public function __construct(Task $task, ?DateTime $previousDate, ?DateTime $currentDate)
    {
        $this->task = $task;
        $this->previousDate = $previousDate;
        $this->currentDate = $currentDate;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getPreviousDate(): ?DateTime
    {
        return $this->previousDate;
    }

    public function getCurrentDate(): ?DateTime
    {
        return $this->currentDate;
    }
}
