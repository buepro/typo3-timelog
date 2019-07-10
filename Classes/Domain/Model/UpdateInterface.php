<?php

/*
 * This file is part of the package buepro/timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Model;

/**
 * A UpdateInterface. Models might present an inconsistent state coming from setting data without using the setters.
 * Models implementing this interface provide methods to update them self for that their state is consistent.
 *
 */
interface UpdateInterface
{
    public function Update();
}
