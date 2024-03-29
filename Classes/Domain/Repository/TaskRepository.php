<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Repository;

use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Domain\Model\Task;
use Buepro\Timelog\Domain\Model\TaskGroup;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * The repository for Tasks
 */
class TaskRepository extends Repository
{
    protected $defaultOrderings = ['uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING];

    /**
     * Sets parameters when used in BE.
     */
    public function initializeObject(): void
    {
        if (($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()
        ) {
            /** @var Typo3QuerySettings $querySettings */
            $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
            $querySettings->setRespectStoragePage(false);
            $this->setDefaultQuerySettings($querySettings);
        }
    }

    /**
     * Gets most recent changed tasks for a project. The tstamp field isn't used since it could be altered when
     * several tasks are updated through the list module.
     *
     * ATTENTION: The storage location isn't considered.
     *
     * @return array|QueryResultInterface
     * @throws Exception
     */
    public function findRecentForProject(Project $project, int $limit = 2, bool $excludeBatched = true)
    {
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_timelog_domain_model_task');
        $queryBuilder
            ->add('select', 'task.uid, MAX(interval.start_time) AS max_start_time')
            ->from('tx_timelog_domain_model_task', 'task')
            ->leftJoin(
                'task',
                'tx_timelog_domain_model_interval',
                'interval',
                $queryBuilder->expr()->eq('task.uid', $queryBuilder->quoteIdentifier('interval.task'))
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'task.project',
                    $queryBuilder->createNamedParameter($project->getUid(), \PDO::PARAM_INT)
                )
            )
            ->groupBy('task.uid')
            ->orderBy('max_start_time', 'DESC')
            ->setMaxResults($limit);

        if ($excludeBatched) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq(
                'batch_date',
                $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
            ));
        }

        //        $sql = $queryBuilder->getSQL();
        //        $params = $queryBuilder->getParameters();

        $result = [];
        foreach($queryBuilder->executeQuery()->fetchAllAssociative() as $record) {
            /** @var array{uid: int} $record */
            $result[] = $this->findByUid($record['uid']);
        }
        return $result;
    }

    /**
     * Gets the available batches for a project or task group.
     * If the scope is a `Project` and `includeAllBatches` is set all batches assigned to a project will be gathered.
     * In case it isn't set batches assigned to a task group won't be included.
     *
     * @param Project|TaskGroup $scope
     * @param bool $includeAllBatches Used in case the scope is a `Project` to control inclusion from batches
     * @throws \Exception
     */
    public function getBatches($scope, bool $includeAllBatches = true): array
    {
        if ($scope instanceof Project) {
            $fieldName = 'task.project';
        } else {
            $fieldName = 'task.task_group';
        }
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_timelog_domain_model_task');
        $queryBuilder
            ->add(
                'select',
                'task.uid AS aTaskUid, task.project AS project, task.task_group AS taskGroup, ' .
                'task.batch_date AS timestamp , SUM(task.active_time) AS sumActiveTime'
            )
            ->from('tx_timelog_domain_model_task', 'task')
            ->andWhere(
                $queryBuilder->expr()->eq(
                    $fieldName,
                    $queryBuilder->createNamedParameter($scope->getUid(), \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->neq(
                    'task.batch_date',
                    0
                )
            )
            ->groupBy('task.task_group', 'task.batch_date')
            ->orderBy('task.batch_date', 'DESC');

        if ($scope instanceof Project && !$includeAllBatches) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq(
                    'task.task_group',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                )
            );
        }

        //        $sql = $queryBuilder->getSQL();
        //        $params = $queryBuilder->getParameters();

        $result = $queryBuilder->executeQuery();
        $batches = [];
        while ($batch = $result->fetchAssociative()) {
            /** @var array{timestamp: int, aTaskUid: int} $batch */
            $date = new \DateTime();
            $date->setTimestamp($batch['timestamp']);
            $batches[] = array_merge($batch, [
                'handle' => \Buepro\Timelog\Utility\GeneralUtility::getBatchHandle(
                    $batch['timestamp'],
                    $batch['aTaskUid']
                ),
                'date' => $date
            ]);
        }
        return $batches;
    }

    /**
     * Finds tasks for given filters. In case a filter is `null` it won't be included in the constraints.
     *
     * @param TaskGroup|int|null $taskGroupFilter 0 to exclude all task groups
     * @return array|QueryResultInterface|null
     */
    public function findForFilter(?Project $project = null, ?Task $task = null, $taskGroupFilter = null, int $batchTime = 0)
    {
        if ($project === null && $task === null && !($taskGroupFilter instanceof TaskGroup)) {
            return null;
        }

        $query = $this->createQuery();
        $constraints = [];

        // Project constraint
        if ($project !== null) {
            $constraints['project'] = $query->equals('project', $project);
        }

        // Task constraint
        if ($task !== null) {
            $constraints['task'] = $query->equals('uid', $task);
        }

        // Task group constraint
        if ($taskGroupFilter !== null) {
            $constraints['taskGroup'] = $query->equals('taskGroup', $taskGroupFilter);
        }

        // Batch constraint
        $constraints['batchDate'] = $query->equals('batchDate', $batchTime);

        $query->matching($query->logicalAnd(...$constraints));
        $query->setOrderings(['intervals.startTime' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING]);
        return $query->execute();
    }
}
