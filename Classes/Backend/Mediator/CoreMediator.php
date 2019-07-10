<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Backend\Mediator;

use Buepro\Timelog\Domain\Model\UpdateInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Class CoreMediator
 *
 * Registers the changed models and updates them upon request.
 * Clients ask for an instance of it and register them self by calling `registerChangedObjectUid`.
 * Since clients are from the backend the scope is automatically backend.
 *
 */
class CoreMediator implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Array holding the uid from changed objects. The array has two dimension where the key from the first dimension
     * represents the class name and the key from the second dimension the uid from the changed object.
     *
     * @var array
     */
    private $changedObjectUidList = [];

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);

        // Connects to signal.
        // In trials it was found that this signals is fired once after all DB operations have been finished.
        // Alternatively the hook processDatamap_afterAllOperations might be used (gets fired twice).
        $signalSlotDispatcher->connect(
            \TYPO3\CMS\Backend\Controller\EditDocumentController::class,
            'initAfter',
            self::class,
            'handleEditDocumentInitAfterSignal'
        );
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
        if (!$this->changedObjectUidList) {
            return;
        }
        foreach ($this->changedObjectUidList as $className => $uidList) {
            foreach ($uidList as $uid => $n) {
                $repositoryClassName = str_replace('Model', 'Repository', $className) . 'Repository';
                if (class_exists($repositoryClassName)) {
                    $object = $this->objectManager->get($repositoryClassName)->findByUid($uid);
                    if ($object instanceof UpdateInterface) {
                        $object->update();
                        $this->objectManager->get(PersistenceManager::class)->add($object);
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
    public function handleEditDocumentInitAfterSignal($editDocumentController, $request)
    {
        // Updates objects
        $this->updateChangedObjects();
        // Persists objects
        $this->objectManager->get(PersistenceManager::class)->persistAll();
        // Clears changed object lists
        $this->changedObjectUidList = [];
    }
}
