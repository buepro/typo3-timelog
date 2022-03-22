<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Mediator;

use Buepro\Timelog\Domain\Model\HandleInterface;
use Buepro\Timelog\Service\DatabaseService;
use Buepro\Timelog\Utility\GeneralUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Event\Persistence\EntityPersistedEvent;

class ExtbaseMediator implements SingletonInterface
{
    public function handlePersistEvent(EntityPersistedEvent $event): void
    {
        if (($object = $event->getObject()) instanceof HandleInterface) {
            $this->checkHandle($object);
        }
    }

    protected function checkHandle(HandleInterface $handleObject): void
    {
        if (
            $handleObject instanceof AbstractEntity &&
            ($objectUid = (int)$handleObject->getUid()) > 0 &&
            ($handle = GeneralUtility::encodeHashid($objectUid, get_class($handleObject))) !== $handleObject->getHandle()
        ) {
            // Sets the handle for the model in memory
            $handleObject->setHandle($handle);
            // Sets the handle for the model in the db
            $tableName = strtolower(str_replace('Buepro\\Timelog\\Domain\\Model\\', '', get_class($handleObject)));
            $tableName = 'tx_timelog_domain_model_' . $tableName;
            (\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(DatabaseService::class))->updateRecord(
                $tableName,
                $objectUid,
                'handle',
                $handle
            );
        }
    }
}
