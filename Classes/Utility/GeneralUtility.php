<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Utility;

use Buepro\Timelog\Domain\Model\Task;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

class GeneralUtility
{

    /**
     * Gets an instance from Hashids for a model.
     *
     * @param string $model
     * @return \Hashids\Hashids
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    private static function getHashids(string $model)
    {
        $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
        );
        $timelogConfiguration = $extensionConfiguration->get('timelog');
        $salt = sprintf('%s%s%s', $model, $timelogConfiguration['hashidSalt'], 'Lihdfg!');
        return new \Hashids\Hashids($salt, $timelogConfiguration['hashidLength']);
    }

    /**
     * Encodes a hash for an id from a model.
     *
     * @param int $uid UID from the model
     * @param string $className Qualified namespaced name of the class
     * @return string The hash
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function encodeHashid(int $uid, string $className)
    {
        $hashids = self::getHashids($className);
        return $hashids->encode($uid);
    }

    /**
     * Decodes th id for a model from a hash.
     *
     * @param string $hash
     * @param string $className Qualified namespaced name of the class
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function decodeHashid(string $hash, string $className)
    {
        $hashids = self::getHashids($className);
        return $hashids->decode($hash)[0];
    }

    /**
     * Gets a batch handle from a timestamp and a uid from a task belonging to the batch.
     *
     * @param int $timestamp
     * @param int $taskUid
     * @return string
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function getBatchHandle(int $timestamp, int $taskUid)
    {
        $hashids = self::getHashids(Task::class);
        return $hashids->encode($timestamp, $taskUid);
    }

    /**
     * Decodes a batch handle to get the timestamp and uid from a task belonging to the batch batch.
     *
     * @param string $hash
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function decodeBatchHandle(string $hash)
    {
        $hashids = self::getHashids(Task::class);
        $decoded = $hashids->decode($hash);
        if (!isset($decoded[1])) {
            return [];
        }
        return [
            'timestamp' => $decoded[0],
            'taskUid' => $decoded[1]
        ];
    }

    /**
     * Returns an email address array to be used in a mailer.
     *
     * @param FrontendUser $feUser
     * @return array In the form [email => name] | [email] | []
     */
    public static function getEmailAddress(FrontendUser $feUser)
    {
        if ($feUser->getLastName() || $feUser->getFirstName()) {
            $name = [];
            if ($feUser->getFirstName()) {
                $name[] = $feUser->getFirstName();
            }
            if ($feUser->getMiddleName()) {
                $name[] = $feUser->getMiddleName();
            }
            if ($feUser->getLastName()) {
                $name[] = $feUser->getLastName();
            }
            $name = implode(' ', $name);
        } elseif ($feUser->getName()) {
            $name = $feUser->getName();
        } else {
            $name = '';
        }
        if ($feUser->getEmail() && $name) {
            return [$feUser->getEmail() => $name];
        } elseif ($feUser->getEmail()) {
            return [$feUser->getEmail()];
        }
        return [];
    }
}
