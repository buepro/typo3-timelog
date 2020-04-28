<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Repository;

use Buepro\Timelog\Domain\Model\Project;
use Buepro\Timelog\Utility\DiUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
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
 * The repository for Projects
 */
class ProjectRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @param FrontendUser|null $client
     * @return array
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     */
    public function findAllWithHash(FrontendUser $client = null)
    {
        /* @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
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
        if ($client) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq('task.batch_date', '""'),
                $queryBuilder->expr()->eq('project.client', $client->getUid())
            );
        } else {
            $queryBuilder->where($queryBuilder->expr()->eq('task.batch_date', '""'));
        }

//        $sql = $queryBuilder->getSQL();
//        $params = $queryBuilder->getParameters();

        $projects = $queryBuilder->execute();
        $result = [];
        $propertyMapper = DiUtility::getObject(PropertyMapper::class);
        foreach ($projects as $project) {
            $result[] = $propertyMapper->convert((string) $project['uid'], Project::class);
        }
        return $result;
    }
}
