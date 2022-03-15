<?php

declare(strict_types=1);

namespace Buepro\Timelog\Domain\Model;

interface HandleInterface
{
    public function getHandle(): string;

    /** @return self */
    public function setHandle(string $handle);
}
