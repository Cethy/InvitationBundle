<?php

namespace Cethyworks\InvitationBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

class InvitedUser implements UserInterface
{
    const ROLE_INVITED = 'ROLE_INVITED';

    /**
     * @var InvitationInterface
     */
    protected $invitation;

    function __construct(InvitationInterface $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Returns the roles granted to the user.
     *
     * @return [] The user roles
     */
    public function getRoles()
    {
        return [self::ROLE_INVITED];
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     *
     * @return string The invitation code
     */
    public function getUsername()
    {
        return $this->invitation->getCode();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(){}

    /**
     * @return InvitationInterface
     */
    public function getInvitation()
    {
        return $this->invitation;
    }
}
