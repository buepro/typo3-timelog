<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Model;

use Buepro\Timelog\Event\TaskActiveTimeChangedEvent;
use Buepro\Timelog\Event\TaskBatchDateChangedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Task
 */
class Task extends AbstractEntity implements UpdateInterface, HandleInterface
{

    /**
     * handle
     *
     * @var string
     */
    protected $handle = '';

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * description
     *
     * @var string
     */
    protected $description = '';

    /**
     * The duration in houres
     *
     * @var float
     */
    protected $activeTime = 0;

    /**
     * batchDate
     *
     * @var \DateTime
     */
    protected $batchDate = null;

    /**
     * project
     *
     * @var \Buepro\Timelog\Domain\Model\Project
     */
    protected $project = null;

    /**
     * taskGroup
     *
     * @var \Buepro\Timelog\Domain\Model\TaskGroup
     */
    protected $taskGroup = null;

    /**
     * worker
     *
     * @var FrontendUser
     */
    protected $worker = null;

    /**
     * intervals
     *
     * @var ObjectStorage<Interval>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade remove
     */
    protected $intervals = null;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * __construct
     */
    public function __construct()
    {

        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function injectEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     */
    protected function initStorageObjects(): void
    {
        // @phpstan-ignore-next-line
        $this->intervals = new ObjectStorage();
    }

    private function triggerActiveTimeChangedEvent(float $previousActiveTime, float $newActiveTime): void
    {
        $this->eventDispatcher->dispatch(new TaskActiveTimeChangedEvent($this, $previousActiveTime, $newActiveTime));
    }

    private function triggerBatchDateChangedEvent(?\DateTime $previousDate, ?\DateTime $currentDate): void
    {
        $this->eventDispatcher->dispatch(new TaskBatchDateChangedEvent($this, $previousDate, $currentDate));
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function addInterval(Interval $interval): self
    {
        $this->intervals->attach($interval);
        return $this;
    }

    public function removeInterval(Interval $intervalToRemove): self
    {
        $this->intervals->detach($intervalToRemove);
        return $this;
    }

    /**
     * Returns the intervals
     *
     * @return null|ObjectStorage<Interval> $intervals
     */
    public function getIntervals()
    {
        return $this->intervals;
    }

    /**
     * Sets the intervals
     *
     * @param ObjectStorage<Interval> $intervals
     */
    public function setIntervals(ObjectStorage $intervals): self
    {
        $this->intervals = $intervals;
        return $this;
    }

    public function getActiveTime(): float
    {
        return $this->activeTime;
    }

    public function setActiveTime(float $activeTime): self
    {
        if ($this->activeTime !== $activeTime) {
            $previousActiveTime = $this->activeTime;
            $this->activeTime = $activeTime;
            $this->triggerActiveTimeChangedEvent($previousActiveTime, $activeTime);
        }
        return $this;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function setHandle(string $handle): self
    {
        $this->handle = $handle;
        return $this;
    }

    public function getBatchDate(): ?\DateTime
    {
        return $this->batchDate;
    }

    public function setBatchDate(\DateTime $batchDate): self
    {
        if (
            ($this->getBatchDate() xor $batchDate) ||
            (
                $this->batchDate !== null &&
                ($this->batchDate->getTimestamp() !== $batchDate->getTimestamp())
            )
        ) {
            $previousBatchDate = $this->batchDate;
            $this->batchDate = $batchDate;
            $this->triggerBatchDateChangedEvent($previousBatchDate, $batchDate);
        }
        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;
        return $this;
    }

    /**
     * Returns the taskGroup
     *
     * @return TaskGroup $taskGroup
     */
    public function getTaskGroup(): ?TaskGroup
    {
        return $this->taskGroup;
    }

    public function setTaskGroup(TaskGroup $taskGroup): self
    {
        $this->taskGroup = $taskGroup;
        return $this;
    }

    public function getWorker(): ?FrontendUser
    {
        return $this->worker;
    }

    public function setWorker(FrontendUser $worker): self
    {
        $this->worker = $worker;
        return $this;
    }

    /**
     * Updates a task by calculating the active time
     */
    public function update(): void
    {
        if (($intervals = $this->getIntervals()) === null) {
            return;
        }
        // Calculates the active time
        $activeTime = 0;
        foreach ($intervals as $interval) {

            // Gets star and end timestamp for interval
            $startTime = 0;
            $endTime = 0;
            if ($interval->getStartTime() !== null) {
                $startTime = $interval->getStartTime()->getTimestamp();
            }
            if ($interval->getEndTime() !== null) {
                $endTime = $interval->getEndTime()->getTimestamp();
            }

            // Sets the active time for the interval
            if ($endTime > $startTime) {
                $intervalDuration = $endTime - $startTime;
                $interval->setDuration($intervalDuration / 3600);
                $activeTime += $intervalDuration;
            } else {
                $interval->setDuration(0);
            }
        }

        // Updates the active time for the task
        if ($activeTime > 0) {
            $this->setActiveTime($activeTime / 3600);
        } else {
            $this->setActiveTime(0);
        }
    }

    /**
     * Returns the earliest startTime from the intervals
     * @return \DateTime|int
     */
    public function getStartTime()
    {
        if (($intervals = $this->getIntervals()) === null) {
            return 0;
        }
        $now = time();
        $result = (new \DateTime())->setTimestamp($now);
        foreach ($intervals as $interval) {
            if (
                ($startTime = $interval->getStartTime()) !== null &&
                $startTime->getTimestamp() < $result->getTimestamp()
            ) {
                $result = $startTime;
            }
        }
        return $result->getTimestamp() === $now ? 0 : $result;
    }

    /**
     * Returns the latest endTime from the intervals
     * @return \DateTime|int
     */
    public function getEndTime()
    {
        if (($intervals = $this->getIntervals()) === null) {
            return 0;
        }
        $result = (new \DateTime())->setTimestamp(0);
        foreach ($intervals as $interval) {
            if (
                ($endTime = $interval->getEndTime()) !== null &&
                $endTime->getTimestamp() > $result->getTimestamp()
            ) {
                $result = $endTime;
            }
        }
        return $result->getTimestamp() === 0 ? 0 : $result;
    }
}
