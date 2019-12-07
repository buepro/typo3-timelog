<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Controller;

use Buepro\Timelog\Domain\Model\Project;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

class ProjectController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * projectRepository
     *
     * @var \Buepro\Timelog\Domain\Repository\ProjectRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $projectRepository = null;

    /**
     * Initializes the view before invoking an action method.
     *
     * @param ViewInterface $view The view to be initialized
     */
    protected function initializeView(ViewInterface $view)
    {
        $view->assign('controller', 'Project');
    }

    /**
     * Shows projects with hashed tasks from a client defined by a project handle.
     * In case no project handle is supplied and a be-user is logged in all hashed projects are shown.
     *
     * @param string $projectHandle
     * @throws \TYPO3\CMS\Extbase\Property\Exception
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    public function listAction(string $projectHandle = '')
    {
        $projects = null;
        if (!$projectHandle) {
            $context = GeneralUtility::makeInstance(Context::class);
            if ($context->getPropertyFromAspect('backend.user', 'isLoggedIn')) {
                $projects = $this->projectRepository->findAllWithHash();
            }
        } else {
            /* @var Project $project */
            [$project] = $this->projectRepository->findByHandle($projectHandle);
            $projects = $this->projectRepository->findAllWithHash($project->getClient());
        }
        $this->view->assignMultiple([
            'projectHandle' => $projectHandle,
            'projects' => $projects
        ]);
    }
}
