<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Backend\Mediator;

use Buepro\Timelog\Domain\Model\Interval;
use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Domain\Model\Task;
use Buepro\Timelog\Domain\Model\TaskGroup;
use Buepro\Timelog\Domain\Model\UpdateInterface;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Maintain object state for changed records in the backend.
 *
 * Within the DataHandler scope clients register changed object records. While saving and staying in the edit form
 * the models get updated by handling the `AfterFormEnginePageInitializedEvent`. Upon saving and closing the form
 * the object update is initiated by the Backend\MediatorMiddleware.
 *
 * Remark: In TYPO3 11 the event might not be needed any more.
 */
class DataHandlerMediator implements SingletonInterface
{
    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * The array has two dimension where the key from the first dimension represents the class name and the key from
     * the second dimension the uid from the changed object.
     *
     * @var array
     */
    private $changedObjects = [];

    /**
     * Start processing with all intervals and end with all projects.
     *
     * @var string[]
     */
    private $processingSequence = [Interval::class, Task::class, TaskGroup::class, Project::class];

    public function __construct(PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    public function registerChangedObject(string $objectClassName, int $uid): void
    {
        $this->changedObjects[$objectClassName][$uid] = 1;
    }

    /**
     * A changed object needs to implement the \Buepro\Timelog\Domain\Model\UpdateInterface and a repository
     * needs to be available for it.
     */
    private function updateChangedObjects(): void
    {
        foreach ($this->processingSequence as $className) {
            if (!isset($this->changedObjects[$className])) {
                continue;
            }
            foreach ($this->changedObjects[$className] as $uid => $n) {
                $repositoryClassName = str_replace('Model', 'Repository', $className) . 'Repository';
                if (class_exists($repositoryClassName)) {
                    $repository = GeneralUtility::makeInstance($repositoryClassName);
                    if (!($repository instanceof Repository)) {
                        continue;
                    }
                    // @phpstan-ignore-next-line
                    $object = $repository->findByUid($uid);
                    if ($object instanceof UpdateInterface) {
                        $object->update();
                        $this->persistenceManager->add($object);
                    }
                }
            }
            $this->persistenceManager->persistAll();
        }
    }

    public function process(): void
    {
        if ($this->changedObjects === []) {
            return;
        }
        $this->updateChangedObjects();
        $this->changedObjects = [];
    }
}
