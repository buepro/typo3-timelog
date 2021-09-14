<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Model;

/**
 * Interval
 */
class Interval extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * startTime
     *
     * @var \DateTime
     */
    protected $startTime = null;

    /**
     * endTime
     *
     * @var \DateTime
     */
    protected $endTime = null;

    /**
     * The duration in houres
     *
     * @var float
     */
    protected $duration = 0;

    /**
     * Returns the startTime
     *
     * @return \DateTime $startTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Sets the startTime
     *
     * @param \DateTime $startTime
     * @return void
     */
    public function setStartTime(\DateTime $startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * Returns the endTime
     *
     * @return \DateTime $endTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Sets the endTime
     *
     * @param \DateTime $endTime
     * @return void
     */
    public function setEndTime(\DateTime $endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * Returns the duration
     *
     * @return float duration
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Sets the duration
     *
     * @param int $duration
     * @return void
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }
}
