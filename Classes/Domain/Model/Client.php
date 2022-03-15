<?php

/*
 * This file is part of the composer package buepro/typo3-timelog.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Buepro\Timelog\Domain\Model;

class Client extends FrontendUser
{
    /**
     * @var string
     */
    protected $ownerEmail = '';

    /**
     * Sets the ownerEmail value
     *
     * @param string $ownerEmail
     */
    public function setOwnerEmail($ownerEmail)
    {
        $this->ownerEmail = $ownerEmail;
    }

    /**
     * Returns the ownerEmail value
     *
     * @return string
     */
    public function getOwnerEmail()
    {
        return $this->ownerEmail;
    }
}
