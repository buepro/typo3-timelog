<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * TaskGroup
 */
class TaskGroup extends AbstractEntity implements UpdateInterface
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
     * internalNote
     *
     * @var string
     */
    protected $internalNote = '';

    /**
     * timeTarget in hours
     *
     * @var float
     */
    protected $timeTarget = 0.0;

    /**
     * timeDeviation in hours
     *
     * @var float
     */
    protected $timeDeviation = 0.0;

    /**
     * Sum from the tasks activeTime in hours
     *
     * @var float
     */
    protected $activeTime = 0.0;

    /**
     * Sum from active time from tasks that are not batched yet
     *
     * @var float
     */
    protected $heapTime = 0.0;

    /**
     * Sum from active time from tasks that are batched
     *
     * @var float
     */
    protected $batchTime = 0.0;

    /**
     * project
     *
     * @var Project
     */
    protected $project = null;

    /**
     * tasks
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Buepro\Timelog\Domain\Model\Task>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade remove
     */
    protected $tasks = null;

    /**
     * __construct
     */
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
        $this->tasks = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
        return $this;
    }

    public function getTimeDeviation(): float
    {
        return $this->timeDeviation;
    }

    public function setTimeDeviation(float $timeDeviation): self
    {
        $this->timeDeviation = $timeDeviation;
        return $this;
    }

    public function getActiveTime(): float
    {
        return $this->activeTime;
    }

    public function setActiveTime(float $activeTime): self
    {
        $this->activeTime = $activeTime;
        $this->update();
        return $this;
    }

    public function getHeapTime(): float
    {
        return $this->heapTime;
    }

    public function setHeapTime(float $heapTime): self
    {
        $this->heapTime = $heapTime;
        return $this;
    }

    public function getBatchTime(): float
    {
        return $this->batchTime;
    }

    public function setBatchTime(float $batchTime): self
    {
        $this->batchTime = $batchTime;
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

    public function addTask(Task $task): self
    {
        $this->tasks->attach($task);
        return $this;
    }

    public function removeTask(Task $taskToRemove): self
    {
        $this->tasks->detach($taskToRemove);
        return $this;
    }

    /**
     * Returns the tasks
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Buepro\Timelog\Domain\Model\Task> $tasks
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Sets the tasks
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Buepro\Timelog\Domain\Model\Task> $tasks
     */
    public function setTasks(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $tasks): self
    {
        $this->tasks = $tasks;
        return $this;
    }

    /**
     * Updates the object state
     */
    public function update(): void
    {
        $this->timeDeviation = $this->getTimeTarget() - $this->getActiveTime();
    }
}
