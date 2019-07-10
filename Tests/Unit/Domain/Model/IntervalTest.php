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
class IntervalTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \Buepro\Timelog\Domain\Model\Interval
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \Buepro\Timelog\Domain\Model\Interval();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getStartTimeReturnsInitialValueForDateTime()
    {
        self::assertEquals(
            null,
            $this->subject->getStartTime()
        );
    }

    /**
     * @test
     */
    public function setStartTimeForDateTimeSetsStartTime()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setStartTime($dateTimeFixture);

        self::assertAttributeEquals(
            $dateTimeFixture,
            'startTime',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getEndTimeReturnsInitialValueForDateTime()
    {
        self::assertEquals(
            null,
            $this->subject->getEndTime()
        );
    }

    /**
     * @test
     */
    public function setEndTimeForDateTimeSetsEndTime()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setEndTime($dateTimeFixture);

        self::assertAttributeEquals(
            $dateTimeFixture,
            'endTime',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDurationReturnsInitialValueForFloat()
    {
        self::assertSame(
            0.0,
            $this->subject->getDuration()
        );
    }

    /**
     * @test
     */
    public function setDurationForFloatSetsDuration()
    {
        $this->subject->setDuration(3.14159265);

        self::assertAttributeEquals(
            3.14159265,
            'duration',
            $this->subject,
            '',
            0.000000001
        );
    }
}
