<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Interval
 */
class Interval extends AbstractEntity implements UpdateInterface
{
    /** @var Task */
    protected $task;

    /** @var \DateTime */
    protected $startTime = null;

    /** @var \DateTime */
    protected $endTime = null;

    /** @var float Unit is hours */
    protected $duration = 0.0;

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(Task $task): self
    {
        $this->task = $task;
        $task->update();
        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): self
    {
        if ($this->startTime === null || ($this->startTime->getTimestamp() !== $startTime->getTimestamp())) {
            $this->startTime = $startTime;
            $this->update();
        }
        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): self
    {
        if ($this->endTime === null || ($this->endTime->getTimestamp() !== $endTime->getTimestamp())) {
            $this->endTime = $endTime;
            $this->update();
        }
        return $this;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function update(): void
    {
        $previousDuration = $this->duration;
        if ($this->startTime === null || $this->endTime === null) {
            $this->duration = 0.0;
        }
        if ($this->startTime !== null && $this->endTime !== null) {
            $this->duration = ($this->endTime->getTimestamp() - $this->startTime->getTimestamp()) / 3600;
        }
        if ($this->task !== null && (abs($previousDuration - $this->duration) > 0.0001)) {
            $this->task->update();
        }
    }
}
