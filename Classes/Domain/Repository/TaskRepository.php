<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Repository;

use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Domain\Model\Task;
use Buepro\Timelog\Utility\DiUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\Property\PropertyMapper;

/***
 *
 * This file is part of the "Timelog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Roman Büchler <rb@buechler.pro>, buechler.pro
 *
 ***/
/**
 * The repository for Tasks
 */
class TaskRepository extends Repository
{
    protected $defaultOrderings = ['uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING];

    /**
     * Sets parameters when used in BE.
     */
    public function initializeObject()
    {
        if (TYPO3_MODE === 'BE') {
            $querySettings = DiUtility::getObject(Typo3QuerySettings::class);
            $querySettings->setRespectStoragePage(false);
            $this->setDefaultQuerySettings($querySettings);
        }
    }

    /**
     * Outputs debug information for a query
     *
     * @param Query $query
     */
    private function debugQuery(Query $query)
    {
        $queryParser = DiUtility::getObject(\TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser::class);
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($queryParser->convertQueryToDoctrineQueryBuilder($query)->getSQL());
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($queryParser->convertQueryToDoctrineQueryBuilder($query)->getParameters());
    }

    /**
     * @param Project $project
     * @param int $timestamp
     * @return array
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     */
    private function findTasks(Project $project, int $timestamp)
    {
        /* @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_timelog_domain_model_task');

        $queryBuilder
            ->add('select', 'task.*, MAX(interval.start_time) AS max_start_time')
            ->from('tx_timelog_domain_model_task', 'task')
            ->leftJoin(
                'task',
                'tx_timelog_domain_model_interval',
                'interval',
                $queryBuilder->expr()->eq('task.uid', $queryBuilder->quoteIdentifier('interval.task'))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('task.project', $queryBuilder->createNamedParameter($project->getUid(), \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('task.batch_date', $queryBuilder->createNamedParameter($timestamp, \PDO::PARAM_INT))
            )
            ->groupBy('task.uid')
            ->orderBy('max_start_time', 'DESC');

//        $sql = $queryBuilder->getSQL();
//        $params = $queryBuilder->getParameters();

        $tasks = $queryBuilder->execute();
        $result = [];
        $propertyMapper = DiUtility::getObject(PropertyMapper::class);
        foreach ($tasks as $task) {
            $result[] = $propertyMapper->convert((string) $task['uid'], Task::class);
        }
        return $result;
    }

    /**
     * Finds all tasks for a project that don't belong to a batch yet.
     *
     * @param Project $project
     * @return array
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     */
    public function findHeapTasks(Project $project)
    {
        return $this->findTasks($project, 0);
    }

    /**
     * Finds all tasks from a batch for a project.
     *
     * @param string $batchHandle
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     */
    public function findBatchTasks(string $batchHandle)
    {
        $decodedBatchHandle = \Buepro\Timelog\Utility\GeneralUtility::decodeBatchHandle($batchHandle);
        if (!$decodedBatchHandle) {
            return [];
        }
        /* @var Task $task */
        $task = $this->findByUid($decodedBatchHandle['taskUid']);
        $project = null;
        if ($task) {
            $project = $task->getProject();
        }
        return $this->findTasks($project, $decodedBatchHandle['timestamp']);
    }

    /**
     * Gets most recent changed tasks for a project. The tstamp field isn't used since it could be altered when
     * several tasks are updated through the list module.
     *
     * ATTENTION: The storage location isn't considered.
     *
     * @param Project $project
     * @param int $limit
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     */
    public function findRecentForProject(Project $project, int $limit = 2)
    {
        /* @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
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

//        $sql = $queryBuilder->getSQL();
//        $params = $queryBuilder->getParameters();

        $tasks = $queryBuilder->execute();

        $result = [];
        $propertyMapper = DiUtility::getObject(PropertyMapper::class);
        foreach ($tasks as $task) {
            $result[] = $propertyMapper->convert((string) $task['uid'], Task::class);
        }
        return $result;
    }

    /**
     * Gets the available batches for a project.
     *
     * @param $project
     * @return array
     * @throws \Exception
     */
    public function getBatches($project)
    {
        if (!$project) {
            return [];
        }

        /* @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_timelog_domain_model_task');
        $queryBuilder
            ->add(
                'select',
                'task.uid AS aTaskUid, task.batch_date AS timestamp, SUM(task.active_time) AS sumActiveTime'
            )
            ->from('tx_timelog_domain_model_task', 'task')
            ->andWhere(
                $queryBuilder->expr()->eq(
                    'task.project',
                    $queryBuilder->createNamedParameter($project->getUid(), \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->neq(
                    'task.batch_date',
                    0
                )
            )
            ->groupBy('task.batch_date')
            ->orderBy('task.batch_date', 'DESC');

//        $sql = $queryBuilder->getSQL();
//        $params = $queryBuilder->getParameters();

        $queryResult = $queryBuilder->execute();

        if ($queryResult->rowCount()) {
            $batches = [];
            foreach ($queryResult as $batch) {
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
        return [];
    }
}
