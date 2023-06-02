<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Model;

use Buepro\Timelog\Event\TaskProjectChangeEvent;
use Buepro\Timelog\Event\TaskTaskGroupChangeEvent;
use Buepro\Timelog\Event\TaskTimeChangeEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Task
 */
class Task extends AbstractEntity implements UpdateInterface, HandleInterface
{
    /** @var string */
    protected $handle = '';

    /** @var string */
    protected $title = '';

    /** @var string */
    protected $description = '';

    /** @var float Unit is hours */
    protected $activeTime = 0;

    /** @var \DateTime */
    protected $batchDate = null;

    /** @var ?Project */
    protected $project = null;

    /** @var ?TaskGroup */
    protected $taskGroup = null;

    /** @var ?FrontendUser */
    protected $worker = null;

    /**
     * @var ObjectStorage<Interval>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade remove
     */
    protected $intervals = null;

    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
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
        $this->update();
        return $this;
    }

    public function removeInterval(Interval $intervalToRemove): self
    {
        $this->intervals->detach($intervalToRemove);
        $this->update();
        return $this;
    }

    /**
     * @return ?ObjectStorage<Interval>
     */
    public function getIntervals()
    {
        return $this->intervals;
    }

    /** @param ObjectStorage<Interval> $intervals */
    public function setIntervals(ObjectStorage $intervals): self
    {
        $this->intervals = $intervals;
        $this->update();
        return $this;
    }

    public function getActiveTime(): float
    {
        return $this->activeTime;
    }

    public function setActiveTime(float $activeTime): self
    {
        if (abs($this->activeTime - $activeTime) > 0.0001) {
            $this->activeTime = $activeTime;
            GeneralUtility::makeInstance(EventDispatcherInterface::class)
                ->dispatch(new TaskTimeChangeEvent($this));
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
        $this->batchDate = $batchDate;
        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        if ($this->project === null && $project === null) {
            return $this;
        }
        if (
            ($this->project === null ||  $project === null) ||
            ($this->project->getUid() !== $project->getUid())
        ) {
            $previousProject = $this->project;
            $this->project = $project;
            GeneralUtility::makeInstance(EventDispatcherInterface::class)
                ->dispatch(new TaskProjectChangeEvent($this, $previousProject, $project));
        }
        return $this;
    }

    public function getTaskGroup(): ?TaskGroup
    {
        return $this->taskGroup;
    }

    public function setTaskGroup(?TaskGroup $taskGroup): self
    {
        if ($this->taskGroup === null && $taskGroup === null) {
            return $this;
        }
        if (
            ($this->taskGroup === null || $taskGroup === null) ||
            ($this->taskGroup->getUid() !== $taskGroup->getUid())
        ) {
            $previousTaskGroup = $this->taskGroup;
            $this->taskGroup = $taskGroup;
            GeneralUtility::makeInstance(EventDispatcherInterface::class)
                ->dispatch(new TaskTaskGroupChangeEvent($this, $previousTaskGroup, $taskGroup));
        }
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

    public function update(): void
    {
        if (($intervals = $this->getIntervals()) === null) {
            return;
        }
        $activeTime = 0;
        foreach ($intervals as $interval) {
            /** @var Interval $interval */
            $activeTime += $interval->getDuration();
        }
        $this->setActiveTime($activeTime);
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
                $interval !== null &&
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
                $interval !== null &&
                ($endTime = $interval->getEndTime()) !== null &&
                $endTime->getTimestamp() > $result->getTimestamp()
            ) {
                $result = $endTime;
            }
        }
        return $result->getTimestamp() === 0 ? 0 : $result;
    }
}
