<?php


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
