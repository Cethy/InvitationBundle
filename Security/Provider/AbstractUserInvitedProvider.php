<?php

namespace Cethyworks\InvitationBundle\Security\Provider;

use Cethyworks\InvitationBundle\Model\InvitationInterface;
use Cethyworks\InvitationBundle\Model\InvitedUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

abstract class AbstractUserInvitedProvider implements UserProviderInterface
{
    /**
     * Return an Invitation or null if not found
     * @return null|InvitationInterface
     */
    abstract protected function getInvitation($code);

    /**
     * Loads the user for the given invitation code.
     *
     * @param string $code The invitation code
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($code)
    {
        if($invitation = $this->getInvitation($code)) {
            return new InvitedUser($invitation);
        }

        throw new UsernameNotFoundException(
            sprintf('Invitation code "%s" does not exist.', $code)
        );
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        /** @var InvitedUser $user */
        if(! $this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }
        //throw new \Exception();
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return InvitedUser::class === $class;
    }

    /**
     * @param string $invitationClass
     * @throws \LogicException
     */
    protected function checkInvitationClass($invitationClass)
    {
        if(! in_array(InvitationInterface::class, class_implements($invitationClass))) {
            throw new \LogicException(sprintf('%s must implements the %s interface.', $invitationClass, InvitationInterface::class));
        }
    }
}
