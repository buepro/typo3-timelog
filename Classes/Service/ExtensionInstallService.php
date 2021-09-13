<?php
declare(strict_types = 1);

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Service;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Package\Event\AfterPackageActivationEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionInstallService
{
    public function __invoke(AfterPackageActivationEvent $event): void
    {
        if ($event->getPackageKey() !== 'timelog') {
            return;
        }
        // Ensure hashid salt is set
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $timelogConfiguration = $extensionConfiguration->get('timelog');
        if ($timelogConfiguration['hashidSalt'] === '') {
            $timelogConfiguration['hashidSalt'] = md5(sprintf('%d Lihdfg!', time()));
            $extensionConfiguration->set('timelog', $timelogConfiguration);
        }
    }
}
