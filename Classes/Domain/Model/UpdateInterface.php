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
 * Models implementing this interface update properties depending on child elements. Mainly it is used to calculate
 * the heap and batch time.
 */
interface UpdateInterface
{
    public function update(): void;
}
