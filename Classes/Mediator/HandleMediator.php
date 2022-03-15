<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Mediator;

use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Domain\Model\Task;
use Buepro\Timelog\Domain\Model\TaskGroup;
use Buepro\Timelog\Service\DatabaseService;
use Buepro\Timelog\Utility\GeneralUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Event\Persistence\EntityPersistedEvent;

/**
 * Class HandleMediator
 *
 * Sets the handle for tasks and projects.
 *
 */
class HandleMediator implements SingletonInterface
{
    public function handlePersistEvent(EntityPersistedEvent $event): void
    {
        $object = $event->getObject();
        if (in_array(get_class($object), [Project::class, Task::class, TaskGroup::class], true) && !$object->getHandle()) {
            $handle = GeneralUtility::encodeHashid($object->getUid(), get_class($object));
            // Sets the handle for the model in memory
            $object->setHandle($handle);
            // Sets the handle for the model in the db
            $tableName = strtolower(str_replace('Buepro\\Timelog\\Domain\\Model\\', '', get_class($object)));
            $tableName = 'tx_timelog_domain_model_' . $tableName;
            (\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(DatabaseService::class))->updateRecord(
                $tableName,
                $object->getUid(),
                'handle',
                $handle
            );
        }
    }
}
