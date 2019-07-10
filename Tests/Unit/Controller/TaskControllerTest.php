<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Tests\Unit\Controller;

/**
 * Test case.
 *
 */
class TaskControllerTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Buepro\Timelog\Controller\TaskController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\Buepro\Timelog\Controller\TaskController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllTasksFromRepositoryAndAssignsThemToView()
    {
        $allTasks = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $taskRepository = $this->getMockBuilder(\Buepro\Timelog\Domain\Repository\TaskRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $taskRepository->expects(self::once())->method('findAll')->will(self::returnValue($allTasks));
        $this->inject($this->subject, 'taskRepository', $taskRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('tasks', $allTasks);
        $this->inject($this->subject, 'view', $view);

        $this->subject->listAction();
    }
}
