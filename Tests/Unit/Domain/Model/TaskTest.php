<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 */
class TaskTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Buepro\Timelog\Domain\Model\Task
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Buepro\Timelog\Domain\Model\Task();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getHandleReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getHandle()
        );
    }

    /**
     * @test
     */
    public function setHandleForStringSetsHandle()
    {
        $this->subject->setHandle('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'handle',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->subject->setTitle('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'title',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription()
    {
        $this->subject->setDescription('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'description',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getActiveTimeReturnsInitialValueForFloat()
    {
        self::assertSame(
            0.0,
            $this->subject->getActiveTime()
        );
    }

    /**
     * @test
     */
    public function setActiveTimeForFloatSetsActiveTime()
    {
        $this->subject->setActiveTime(3.14159265);

        self::assertAttributeEquals(
            3.14159265,
            'activeTime',
            $this->subject,
            '',
            0.000000001
        );
    }

    /**
     * @test
     */
    public function getBatchStateReturnsInitialValueForInt()
    {
        self::assertSame(
            0,
            $this->subject->getBatchState()
        );
    }

    /**
     * @test
     */
    public function setBatchStateForIntSetsBatchState()
    {
        $this->subject->setBatchState(12);

        self::assertAttributeEquals(
            12,
            'batchState',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getBatchDateReturnsInitialValueForDateTime()
    {
        self::assertEquals(
            null,
            $this->subject->getBatchDate()
        );
    }

    /**
     * @test
     */
    public function setBatchDateForDateTimeSetsBatchDate()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setBatchDate($dateTimeFixture);

        self::assertAttributeEquals(
            $dateTimeFixture,
            'batchDate',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getProjectReturnsInitialValueForProject()
    {
        self::assertEquals(
            null,
            $this->subject->getProject()
        );
    }

    /**
     * @test
     */
    public function setProjectForProjectSetsProject()
    {
        $projectFixture = new \Buepro\Timelog\Domain\Model\Project();
        $this->subject->setProject($projectFixture);

        self::assertAttributeEquals(
            $projectFixture,
            'project',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getWorkerReturnsInitialValueForFrontendUser()
    {
    }

    /**
     * @test
     */
    public function setWorkerForFrontendUserSetsWorker()
    {
    }

    /**
     * @test
     */
    public function getIntervalsReturnsInitialValueForInterval()
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getIntervals()
        );
    }

    /**
     * @test
     */
    public function setIntervalsForObjectStorageContainingIntervalSetsIntervals()
    {
        $interval = new \Buepro\Timelog\Domain\Model\Interval();
        $objectStorageHoldingExactlyOneIntervals = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneIntervals->attach($interval);
        $this->subject->setIntervals($objectStorageHoldingExactlyOneIntervals);

        self::assertAttributeEquals(
            $objectStorageHoldingExactlyOneIntervals,
            'intervals',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function addIntervalToObjectStorageHoldingIntervals()
    {
        $interval = new \Buepro\Timelog\Domain\Model\Interval();
        $intervalsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $intervalsObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($interval));
        $this->inject($this->subject, 'intervals', $intervalsObjectStorageMock);

        $this->subject->addInterval($interval);
    }

    /**
     * @test
     */
    public function removeIntervalFromObjectStorageHoldingIntervals()
    {
        $interval = new \Buepro\Timelog\Domain\Model\Interval();
        $intervalsObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->setMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $intervalsObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($interval));
        $this->inject($this->subject, 'intervals', $intervalsObjectStorageMock);

        $this->subject->removeInterval($interval);
    }
}
