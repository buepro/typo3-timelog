<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Utility;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class DatabaseUtility
 */
class DatabaseUtility
{

    /**
     * Sets a field from a table
     *
     * @param string $tableName
     * @param int $recordUid Uid from the record to be updated
     * @param string $fieldName
     * @param $fieldValue
     * @return int The number of affected rows
     */
    public static function updateRecord(string $tableName, int $recordUid, string $fieldName, $fieldValue)
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($tableName)
            ->update($tableName, [$fieldName => $fieldValue], ['uid' => $recordUid]);
    }

    /**
     * Gets a record by its uid
     *
     * @param string $tableName
     * @param int $uid
     * @param array $fields
     * @return mixed
     */
    public static function getRecordByUid(string $tableName, int $uid, array $fields)
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($tableName)
            ->select($fields, $tableName, ['uid' => $uid])
            ->fetch();
    }

    /**
     * Returns the record with the highest uid
     *
     * @param string $table Table name present in $GLOBALS['TCA']
     * @param string $fields List of fields to select
     * @param string $where Additional WHERE clause, eg. " AND blablabla = 0
     * @param bool $useDeleteClause Use the deleteClause to check if a record is deleted (default TRUE)
     * @return mixed|null
     */
    public static function getLatestRecord(string $table, $fields = '*', $where = '', $useDeleteClause = true)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        // should the delete clause be used
        if ($useDeleteClause) {
            $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        }

        // set table and where clause
        $queryBuilder
            ->select(...GeneralUtility::trimExplode(',', $fields, true))
            ->from($table)
            ->addOrderBy('uid', 'DESC')
            ->setMaxResults(1);

        // add custom where clause
        if ($where) {
            $queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($where));
        }

        $row = $queryBuilder->execute()->fetch();
        if ($row) {
            return $row;
        }
        return null;
    }

    /**
     * Checks whether a task is in progress by checking if there is an interval that has no end_time defined.
     * This method is intended to be used in the BE.
     *
     * @param int $taskUid
     * @return bool
     */
    public static function taskIsInProgress(int $taskUid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_timelog_domain_model_interval');
        $result = $queryBuilder
            ->select('*')
            ->from('tx_timelog_domain_model_interval')
            ->where(
                $queryBuilder->expr()->eq('task', $queryBuilder->createNamedParameter($taskUid, \PDO::PARAM_INT)),
                $queryBuilder->expr()->gt('start_time', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('end_time', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
            )
            ->execute();
        if ($result) {
            return $result->rowCount() > 0;
        }
        return false;
    }
}
