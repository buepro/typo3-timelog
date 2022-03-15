<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Model;

/**
 * Project
 */
class Project extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity implements \Buepro\Timelog\Domain\Model\UpdateInterface
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
     * client
     *
     * @var Client
     */
    protected $client = null;

    /**
     * owner
     *
     * @var FrontendUser
     */
    protected $owner = null;

    /**
     * ccEmail
     *
     * @var string
     */
    protected $ccEmail = '';

    /**
     * tasks
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Buepro\Timelog\Domain\Model\Task>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade remove
     */
    protected $tasks = null;

    /**
     * taskGroups
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<TaskGroup>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade remove
     */
    protected $taskGroups = null;

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
     */
    protected function initStorageObjects(): void
    {
        // @phpstan-ignore-next-line
        $this->tasks = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        // @phpstan-ignore-next-line
        $this->taskGroups = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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

    public function getActiveTime(): float
    {
        return $this->activeTime;
    }

    public function setActiveTime(float $activeTime): self
    {
        $this->activeTime = $activeTime;
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getOwner(): ?FrontendUser
    {
        return $this->owner;
    }

    public function setOwner(FrontendUser $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    public function getCcEmail(): string
    {
        return $this->ccEmail;
    }

    public function setCcEmail(string $ccEmail): self
    {
        $this->ccEmail = $ccEmail;
        return $this;
    }

    public function addTask(\Buepro\Timelog\Domain\Model\Task $task): self
    {
        $this->tasks->attach($task);
        return $this;
    }

    public function removeTask(\Buepro\Timelog\Domain\Model\Task $taskToRemove): self
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

    public function addTaskGroup(TaskGroup $taskGroup): self
    {
        $this->taskGroups->attach($taskGroup);
        return $this;
    }

    public function removeTaskGroup(TaskGroup $taskGroupToRemove): self
    {
        $this->taskGroups->detach($taskGroupToRemove);
        return $this;
    }

    /**
     * Returns the taskGroups
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<TaskGroup> $taskGroups
     */
    public function getTaskGroups()
    {
        return $this->taskGroups;
    }

    /**
     * Sets the taskGroups
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<TaskGroup> $taskGroups
     */
    public function setTaskGroups(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $taskGroups): self
    {
        $this->taskGroups = $taskGroups;
        return $this;
    }

    /**
     * Updates its state
     *
     * @return void
     */
    public function update(): void
    {
        // TODO: Implement Update() method.
    }
}
