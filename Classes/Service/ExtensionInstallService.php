<?php
declare(strict_types = 1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Service;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extensionmanager\Event\AfterExtensionFilesHaveBeenImportedEvent;

class ExtensionInstallService
{
    public function __invoke(AfterExtensionFilesHaveBeenImportedEvent $event): void
    {
        if ($event->getPackageKey() !== 'timelog') {
            return;
        }
        // Ensure hashid salt is set
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $timelogConfiguration = $extensionConfiguration->get('timelog');
        if (
            is_array($timelogConfiguration) &&
            isset($timelogConfiguration['hashidSalt']) &&
            $timelogConfiguration['hashidSalt'] === ''
        ) {
            $timelogConfiguration['hashidSalt'] = md5(sprintf('%d Lihdfg!', time()));

            // Workaround for
            // https://review.typo3.org/c/Packages/TYPO3.CMS/+/62650
            $reflection = new \ReflectionClass(ExtensionConfiguration::class);
            $parameters = $reflection->getMethod('set')->getParameters();
            $arguments = [];
            $arguments[] = 'timelog';
            if (count($parameters) === 3) {
                $arguments[] = '';
            }
            $arguments[] = $timelogConfiguration;

            $extensionConfiguration->set(...$arguments);
        }
    }
}
