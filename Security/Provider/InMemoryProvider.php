<?php

namespace Cethyworks\InvitationBundle\Security\Provider;

use Cethyworks\InvitationBundle\Model\EmailInvitationInterface;
use Cethyworks\InvitationBundle\Model\InvitationInterface;

class InMemoryProvider extends AbstractUserInvitedProvider
{
    /**
     * @var InvitationInterface[]
     */
    private $invitations;

    /**
     * Constructor.
     *
     * The invitation array is a hash where the keys are irrelevant and the values are
     * an array of attributes: 'code' and 'email'.
     *
     * @param string $invitationClass (must implements `InvitationInterface`)
     * @param array $invitations
     */
    public function __construct($invitationClass, array $invitations = [])
    {
        $this->checkInvitationClass($invitationClass);

        foreach ($invitations as $invitationData) {
            /** @var InvitationInterface $invitation */
            $invitation = new $invitationClass();

            $invitation
                ->setCode(isset($invitationData['code']) ? $invitationData['code'] : '')
            ;

            /** @var EmailInvitationInterface $invitation */
            if($invitation instanceof EmailInvitationInterface) {
                $invitation
                    ->setEmail(isset($invitationData['email']) ? $invitationData['email'] : '')
                ;
            }

            $this->invitations[] = $invitation;
        }
    }

    /**
     * Return an Invitation or null if not found
     * @return null|InvitationInterface
     */
    protected function getInvitation($code)
    {
        /** @var InvitationInterface $invitation */
        foreach($this->invitations as $invitation) {
            if($invitation->getCode() == $code) {
                return $invitation;
            }
        }

        return null;
    }
}
