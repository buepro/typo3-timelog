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
 * Project
 */
class Project extends AbstractEntity implements UpdateInterface, HandleInterface
{

    /** @var string */
    protected $handle = '';

    /** @var string */
    protected $title = '';

    /** @var string */
    protected $description = '';

    /** @var string */
    protected $internalNote = '';

    /**  @var float Unit is hours */
    protected $activeTime = 0.0;

    /** @var float Unit is hours */
    protected $heapTime = 0.0;

    /** @var float Unit is hours */
    protected $batchTime = 0.0;

    /** @var ?Client */
    protected $client = null;

    /** @var ?FrontendUser */
    protected $owner = null;

    /** @var string */
    protected $ccEmail = '';

    /**
     * @var null|ObjectStorage<Task>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade remove
     */
    protected $tasks = null;

    /**
     * @var null|ObjectStorage<TaskGroup>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade remove
     */
    protected $taskGroups = null;

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
        $this->tasks = new ObjectStorage();
        // @phpstan-ignore-next-line
        $this->taskGroups = new ObjectStorage();
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

    public function getHeapTime(): float
    {
        return $this->heapTime;
    }

    public function getBatchTime(): float
    {
        return $this->batchTime;
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

    /** @return ?ObjectStorage<Task> */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**  @return ?ObjectStorage<TaskGroup> */
    public function getTaskGroups()
    {
        return $this->taskGroups;
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
    }
}
