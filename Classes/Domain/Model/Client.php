<?php

declare(strict_types=1);

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

    public function setOwnerEmail(string $ownerEmail): self
    {
        $this->ownerEmail = $ownerEmail;
        return $this;
    }

    public function getOwnerEmail(): string
    {
        return $this->ownerEmail;
    }
}
