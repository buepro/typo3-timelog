<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Model;

use Buepro\Timelog\Event\TaskActiveTimeChangedEvent;
use Buepro\Timelog\Event\TaskBatchDateChangedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Task
 */
class Task extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity implements \Buepro\Timelog\Domain\Model\UpdateInterface
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
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $worker = null;

    /**
     * intervals
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Buepro\Timelog\Domain\Model\Interval>
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
    public function injectEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->intervals = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * @param float $previousActiveTime
     * @param float $newActiveTime
     */
    private function triggerActiveTimeChangedEvent(float $previousActiveTime, float $newActiveTime)
    {
        $this->eventDispatcher->dispatch(new TaskActiveTimeChangedEvent($this, $previousActiveTime, $newActiveTime));
    }

    /**
     * @param \DateTime|null $previousDate
     * @param \DateTime|null $currentDate
     */
    private function triggerBatchDateChangedEvent($previousDate, $currentDate)
    {
        $this->eventDispatcher->dispatch(new TaskBatchDateChangedEvent($this, $previousDate, $currentDate));
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Adds a Interval
     *
     * @param \Buepro\Timelog\Domain\Model\Interval $interval
     * @return void
     */
    public function addInterval(\Buepro\Timelog\Domain\Model\Interval $interval)
    {
        $this->intervals->attach($interval);
    }

    /**
     * Removes a Interval
     *
     * @param \Buepro\Timelog\Domain\Model\Interval $intervalToRemove The Interval to be removed
     * @return void
     */
    public function removeInterval(\Buepro\Timelog\Domain\Model\Interval $intervalToRemove)
    {
        $this->intervals->detach($intervalToRemove);
    }

    /**
     * Returns the intervals
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Buepro\Timelog\Domain\Model\Interval> $intervals
     */
    public function getIntervals()
    {
        return $this->intervals;
    }

    /**
     * Sets the intervals
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Buepro\Timelog\Domain\Model\Interval> $intervals
     * @return void
     */
    public function setIntervals(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $intervals)
    {
        $this->intervals = $intervals;
    }

    /**
     * Returns the activeTime
     *
     * @return float activeTime
     */
    public function getActiveTime()
    {
        return $this->activeTime;
    }

    /**
     * Sets the activeTime
     *
     * @param float $activeTime
     * @return void
     */
    public function setActiveTime(float $activeTime)
    {
        if ($this->activeTime !== $activeTime) {
            $previousActiveTime = $this->activeTime;
            $this->activeTime = $activeTime;
            $this->triggerActiveTimeChangedEvent($previousActiveTime, $activeTime);
        }
    }

    /**
     * Returns the handle
     *
     * @return string $handle
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Sets the handle
     *
     * @param string $handle
     * @return void
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * Returns the batchDate
     *
     * @return \DateTime $batchDate
     */
    public function getBatchDate()
    {
        return $this->batchDate;
    }

    /**
     * Sets the batchDate
     *
     * @param \DateTime $batchDate
     * @return void
     */
    public function setBatchDate(\DateTime $batchDate)
    {
        if (($this->getBatchDate() xor $batchDate) ||
            ($this->batchDate && $batchDate && ($this->batchDate->getTimestamp() !== $batchDate->getTimestamp()))) {
            $previousBatchDate = $this->batchDate;
            $this->batchDate = $batchDate;
            $this->triggerBatchDateChangedEvent($previousBatchDate, $batchDate);
        }
    }

    /**
     * Returns the project
     *
     * @return \Buepro\Timelog\Domain\Model\Project $project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Sets the project
     *
     * @param \Buepro\Timelog\Domain\Model\Project $project
     * @return void
     */
    public function setProject(\Buepro\Timelog\Domain\Model\Project $project)
    {
        $this->project = $project;
    }

    /**
     * Returns the taskGroup
     *
     * @return \Buepro\Timelog\Domain\Model\TaskGroup $taskGroup
     */
    public function getTaskGroup()
    {
        return $this->taskGroup;
    }

    /**
     * Sets the taskGroup
     *
     * @param \Buepro\Timelog\Domain\Model\TaskGroup $taskGroup
     * @return void
     */
    public function setTaskGroup(\Buepro\Timelog\Domain\Model\TaskGroup $taskGroup)
    {
        $this->taskGroup = $taskGroup;
    }

    /**
     * Returns the worker
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $worker
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * Sets the worker
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $worker
     * @return void
     */
    public function setWorker(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $worker)
    {
        $this->worker = $worker;
    }

    /**
     * Updates a task by calculating the active time
     */
    public function update()
    {
        // Calculates the active time
        $activeTime = 0;
        foreach ($this->getIntervals() as $interval) {

            // Gets star and end timestamp for interval
            $startTime = 0;
            $endTime = 0;
            if ($interval->getStartTime()) {
                $startTime = $interval->getStartTime()->getTimestamp();
            }
            if ($interval->getEndTime()) {
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
        $intervals = $this->getIntervals();
        if (!$intervals) {
            return 0;
        }
        $result = $intervals[0]->getStartTime();
        $startTime = $result->getTimestamp();
        foreach ($intervals as $interval) {
            if ($interval->getStartTime()->getTimestamp() < $startTime) {
                $result = $interval->getStartTime();
                $startTime = $result->getTimestamp();
            }
        }
        return $startTime;
    }

    /**
     * Returns the latest endTime from the intervals
     * @return \DateTime|int
     */
    public function getEndTime()
    {
        $intervals = $this->getIntervals();
        if (!$intervals) {
            return 0;
        }
        $result = $intervals[0]->getStartTime();
        $startTime = $result->getTimestamp();
        foreach ($intervals as $interval) {
            if ($interval->getStartTime()->getTimestamp() > $startTime) {
                $result = $interval->getStartTime();
                $startTime = $result->getTimestamp();
            }
        }
        return $startTime;
    }
}
