<?php

namespace Cethyworks\InvitationBundle\Model;

interface EmailInvitationInterface
{
    /**
     * Get email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);
}
