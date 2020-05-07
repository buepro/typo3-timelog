<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Model;

/***
 *
 * This file is part of the "Timelog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Roman Büchler <rb@buechler.pro>, buechler.pro
 *
 ***/
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
     * client
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $client = null;

    /**
     * owner
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $owner = null;

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
    }

    /**
     * Returns the client
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Sets the client
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $client
     * @return void
     */
    public function setClient(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $client)
    {
        $this->client = $client;
    }

    /**
     * Returns the owner
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $owner
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Sets the owner
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $owner
     * @return void
     */
    public function setOwner(\TYPO3\CMS\Extbase\Domain\Model\FrontendUser $owner)
    {
        $this->owner = $owner;
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
     * Returns the ccEmail
     *
     * @return string $ccEmail
     */
    public function getCcEmail()
    {
        return $this->ccEmail;
    }

    /**
     * Sets the ccEmail
     *
     * @param string $ccEmail
     * @return void
     */
    public function setCcEmail($ccEmail)
    {
        $this->ccEmail = $ccEmail;
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

    public function Update()
    {
        // TODO: Implement Update() method.
    }
}
