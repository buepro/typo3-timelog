<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Model;

/**
 * TaskGroup
 */
class TaskGroup extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity implements \Buepro\Timelog\Domain\Model\UpdateInterface
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
        $this->tasks = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
     * Returns the internalNote
     *
     * @return string $internalNote
     */
    public function getInternalNote()
    {
        return $this->internalNote;
    }

    /**
     * Sets the internalNote
     *
     * @param string $internalNote
     * @return void
     */
    public function setInternalNote($internalNote)
    {
        $this->internalNote = $internalNote;
    }

    /**
     * Returns the timeTarget
     *
     * @return float $timeTarget
     */
    public function getTimeTarget()
    {
        return $this->timeTarget;
    }

    /**
     * Sets the timeTarget
     *
     * @param float $timeTarget
     * @return void
     */
    public function setTimeTarget($timeTarget)
    {
        $this->timeTarget = $timeTarget;
    }

    /**
     * Returns the timeDeviation
     *
     * @return float $timeDeviation
     */
    public function getTimeDeviation()
    {
        return $this->timeDeviation;
    }

    /**
     * Sets the timeDeviation
     *
     * @param float $timeDeviation
     * @return void
     */
    public function setTimeDeviation($timeDeviation)
    {
        $this->timeDeviation = $timeDeviation;
    }

    /**
     * Returns the activeTime
     *
     * @return float $activeTime
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
    public function setActiveTime($activeTime)
    {
        $this->activeTime = $activeTime;
        $this->update();
    }

    /**
     * Returns the heapTime
     *
     * @return float $heapTime
     */
    public function getHeapTime()
    {
        return $this->heapTime;
    }

    /**
     * Sets the heapTime
     *
     * @param float $heapTime
     * @return void
     */
    public function setHeapTime($heapTime)
    {
        $this->heapTime = $heapTime;
    }

    /**
     * Returns the batchTime
     *
     * @return float $batchTime
     */
    public function getBatchTime()
    {
        return $this->batchTime;
    }

    /**
     * Sets the batchTime
     *
     * @param float $batchTime
     * @return void
     */
    public function setBatchTime($batchTime)
    {
        $this->batchTime = $batchTime;
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
     * Adds a Task
     *
     * @param \Buepro\Timelog\Domain\Model\Task $task
     * @return void
     */
    public function addTask(\Buepro\Timelog\Domain\Model\Task $task)
    {
        $this->tasks->attach($task);
    }

    /**
     * Removes a Task
     *
     * @param \Buepro\Timelog\Domain\Model\Task $taskToRemove The Task to be removed
     * @return void
     */
    public function removeTask(\Buepro\Timelog\Domain\Model\Task $taskToRemove)
    {
        $this->tasks->detach($taskToRemove);
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
     * @return void
     */
    public function setTasks(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * Updates the object state
     */
    public function update()
    {
        $this->timeDeviation = $this->getTimeTarget() - $this->getActiveTime();
    }
}
