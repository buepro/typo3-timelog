<?php

declare(strict_types=1);

/*
 * This file is part of the composer package buepro/typo3-timelog.
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

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): self
    {
        $this->duration = $duration;
        return $this;
    }
}
