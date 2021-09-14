<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Backend\Mediator;

use Buepro\Timelog\Domain\Model\UpdateInterface;
use TYPO3\CMS\Backend\Controller\Event\AfterFormEnginePageInitializedEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Class CoreMediator
 *
 * Models might be changed in the backend without using setters. This can lead to an not consistent model state.
 * Such models can be registered and updated at this mediator.
 *
 * Clients ask for an instance of it and register them self by calling `registerChangedObjectUid`.
 * Since clients are from the backend the scope is automatically backend.
 *
 */
class CoreMediator implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * Array holding the uid from changed objects. The array has two dimension where the key from the first dimension
     * represents the class name and the key from the second dimension the uid from the changed object.
     *
     * @var array
     */
    private $changedObjectUidList = [];

    public function __construct(PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param string $objectClassName
     * @param int $uid
     */
    public function registerChangedObjectUid(string $objectClassName, int $uid)
    {
        $this->changedObjectUidList[$objectClassName][$uid] = 1;
    }

    /**
     * Updates the changed objects.
     * A changed object needs to implement the \Buepro\Timelog\Domain\Model\UpdateInterface and a repository
     * needs to be available for it.
     */
    private function updateChangedObjects()
    {
        foreach ($this->changedObjectUidList as $className => $uidList) {
            foreach ($uidList as $uid => $n) {
                $repositoryClassName = str_replace('Model', 'Repository', $className) . 'Repository';
                if (class_exists($repositoryClassName)) {
                    $repository = GeneralUtility::makeInstance($repositoryClassName);
                    if (!$repository) {
                        continue;
                    }
                    $object = $repository->findByUid($uid);
                    if ($object && $object instanceof UpdateInterface) {
                        $object->update();
                        $this->persistenceManager->add($object);
                    }
                }
            }
        }
    }

    /**
     * This signal is received after the view for the form has been initialized (data for form elements not fetched yet)
     *
     * @param $editDocumentController
     * @param $request
     */
    public function handleAfterFormEnginePageInitializedEvent(AfterFormEnginePageInitializedEvent $event)
    {
        if (!$this->changedObjectUidList) {
            return;
        }

        // Updates objects
        $this->updateChangedObjects();
        // Persists objects
        $this->persistenceManager->persistAll();
        // Clears changed object lists
        $this->changedObjectUidList = [];
    }
}
