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
class ProjectTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Buepro\Timelog\Domain\Model\Project
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Buepro\Timelog\Domain\Model\Project();
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
    public function getHeapTimeReturnsInitialValueForFloat()
    {
        self::assertSame(
            0.0,
            $this->subject->getHeapTime()
        );
    }

    /**
     * @test
     */
    public function setHeapTimeForFloatSetsHeapTime()
    {
        $this->subject->setHeapTime(3.14159265);

        self::assertAttributeEquals(
            3.14159265,
            'heapTime',
            $this->subject,
            '',
            0.000000001
        );
    }

    /**
     * @test
     */
    public function getBatchTimeReturnsInitialValueForFloat()
    {
        self::assertSame(
            0.0,
            $this->subject->getBatchTime()
        );
    }

    /**
     * @test
     */
    public function setBatchTimeForFloatSetsBatchTime()
    {
        $this->subject->setBatchTime(3.14159265);

        self::assertAttributeEquals(
            3.14159265,
            'batchTime',
            $this->subject,
            '',
            0.000000001
        );
    }

    /**
     * @test
     */
    public function getClientReturnsInitialValueForFrontendUser()
    {
    }

    /**
     * @test
     */
    public function setClientForFrontendUserSetsClient()
    {
    }

    /**
     * @test
     */
    public function getOwnerReturnsInitialValueForFrontendUser()
    {
    }

    /**
     * @test
     */
    public function setOwnerForFrontendUserSetsOwner()
    {
    }
}
