<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Service;

use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RegistryService
{
    public function getWorkerUidForBeUser(): int
    {
        $registry = GeneralUtility::makeInstance(Registry::class);
        $workerUid = $registry->get('tx-timelog-be-user-worker-uid', (string) $GLOBALS['BE_USER']->user['uid'], 0);
        return is_int($workerUid) ? $workerUid : 0;
    }

    public function setWorkerUidForBeUser(int $workerUid): void
    {
        $registry = GeneralUtility::makeInstance(Registry::class);
        $registry->set('tx-timelog-be-user-worker-uid', (string) $GLOBALS['BE_USER']->user['uid'], $workerUid);
    }
}
