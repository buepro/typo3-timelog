<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Model;

use Buepro\Timelog\Utility\TaskUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * TaskGroup
 */
class TaskGroup extends AbstractEntity implements UpdateInterface, HandleInterface
{

    /** @var string */
    protected $handle = '';

    /** @var string */
    protected $title = '';

    /** @var string */
    protected $description = '';

    /** @var string */
    protected $internalNote = '';

    /** @var float Unit is hours */
    protected $timeTarget = 0.0;

    /** @var float Unit is hours */
    protected $timeDeviation = 0.0;

    /** @var float Unit is hours */
    protected $activeTime = 0.0;

    /** @var float Unit is hours */
    protected $heapTime = 0.0;

    /** @var float Unit is hours */
    protected $batchTime = 0.0;

    /** @var ?Project */
    protected $project = null;

    /**
     * @var null|ObjectStorage<Task>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade remove
     */
    protected $tasks = null;

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
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        // @phpstan-ignore-next-line
        $this->tasks = new ObjectStorage();
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

    public function getInternalNote(): string
    {
        return $this->internalNote;
    }

    public function setInternalNote(string $internalNote): self
    {
        $this->internalNote = $internalNote;
        return $this;
    }

    public function getTimeTarget(): float
    {
        return $this->timeTarget;
    }

    public function setTimeTarget(float $timeTarget): self
    {
        $this->timeTarget = $timeTarget;
        $this->updateTimeDeviation();
        return $this;
    }

    public function getTimeDeviation(): float
    {
        return $this->timeDeviation;
    }

    public function getActiveTime(): float
    {
        return $this->activeTime;
    }

    public function getHeapTime(): float
    {
        return $this->heapTime;
    }

    public function getBatchTime(): float
    {
        return $this->batchTime;
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

    /** @return ?ObjectStorage<Task> */
    public function getTasks()
    {
        return $this->tasks;
    }

    public function update(): void
    {
        if (($tasks = $this->getTasks()) === null) {
            return;
        }
        $tasks = $tasks->toArray();
        $this->activeTime = TaskUtility::getActiveTimeForTasks($tasks);
        $this->heapTime = TaskUtility::getHeapTimeForTasks($tasks);
        $this->batchTime = TaskUtility::getBatchTimeForTasks($tasks);
        $this->updateTimeDeviation();
    }

    protected function updateTimeDeviation(): void
    {
        $this->timeDeviation = $this->getTimeTarget() - $this->getActiveTime();
    }
}
