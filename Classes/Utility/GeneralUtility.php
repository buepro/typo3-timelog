<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Utility;

use Buepro\Timelog\Domain\Model\Task;
use Hashids\Hashids;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class GeneralUtility
{
    /**
     * Gets an instance from Hashids for a model.
     *
     * @param string $model
     * @return Hashids
     */
    private static function getHashids(string $model)
    {
        $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            ExtensionConfiguration::class
        );
        $timelogConfiguration = $extensionConfiguration->get('timelog');
        if (!is_array($timelogConfiguration) || !isset($timelogConfiguration['hashidSalt'], $timelogConfiguration['hashidLength'])) {
            throw new \LogicException('The timelog configuration is not available.', 1668537875);
        }
        $salt = sprintf('%s%s%s', $model, $timelogConfiguration['hashidSalt'], 'Lihdfg!');
        return new Hashids($salt, (int)$timelogConfiguration['hashidLength']);
    }

    /**
     * Encodes a hash for an id from a model.
     *
     * @param int $uid UID from the model
     * @param string $className Qualified namespaced name of the class
     * @return string The hash
     */
    public static function encodeHashid(int $uid, string $className)
    {
        return self::getHashids($className)->encode($uid);
    }

    /**
     * Decodes th id for a model from a hash.
     *
     * @param string $hash
     * @param string $className Qualified namespaced name of the class
     * @return int
     */
    public static function decodeHashid(string $hash, string $className)
    {
        return self::getHashids($className)->decode($hash)[0] ?? 0;
    }

    /**
     * Gets a batch handle from a timestamp and a uid from a task belonging to the batch.
     *
     * @param int $timestamp
     * @param int $taskUid
     * @return string
     */
    public static function getBatchHandle(int $timestamp, int $taskUid)
    {
        return self::getHashids(Task::class)->encode($timestamp, $taskUid);
    }

    /**
     * Decodes a batch handle to get the timestamp and uid from a task belonging to the batch batch.
     */
    public static function decodeBatchHandle(string $hash): ?array
    {
        $hashids = self::getHashids(Task::class);
        $decoded = $hashids->decode($hash);
        if (!isset($decoded[1])) {
            return null;
        }
        return [
            'timestamp' => $decoded[0],
            'taskUid' => $decoded[1]
        ];
    }
}
