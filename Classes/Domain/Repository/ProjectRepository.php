<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Repository;

use Buepro\Timelog\Domain\Model\FrontendUser;
use Buepro\Timelog\Domain\Model\Project;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Property\PropertyMapper;

/**
 * The repository for Projects
 */
class ProjectRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @param FrontendUser|null $client
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     */
    public function findAllWithHash(FrontendUser $client = null): array
    {
        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_timelog_domain_model_project');

        $queryBuilder
            ->select('project.*')
            ->from('tx_timelog_domain_model_project', 'project')
            ->innerJoin(
                'project',
                'tx_timelog_domain_model_task',
                'task',
                $queryBuilder->expr()->eq('project.uid', $queryBuilder->quoteIdentifier('task.project'))
            )
            ->groupBy('project.uid')
            ->orderBy('project.tstamp', 'DESC');
        if ($client !== null) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq('task.batch_date', '""'),
                $queryBuilder->expr()->eq('project.client', $client->getUid())
            );
        } else {
            $queryBuilder->where($queryBuilder->expr()->eq('task.batch_date', '""'));
        }

        //        $sql = $queryBuilder->getSQL();
        //        $params = $queryBuilder->getParameters();

        $result = $queryBuilder->executeQuery();
        $propertyMapper = GeneralUtility::makeInstance(PropertyMapper::class);
        $projects = [];
        while ($projectRecord = $result->fetchAssociative()) {
            /** @var array{uid: int} $projectRecord */
            $projects[] = $propertyMapper->convert((string) $projectRecord['uid'], Project::class);
        }
        return $projects;
    }
}
