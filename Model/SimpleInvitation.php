<?php

namespace Cethyworks\InvitationBundle\Model;

/**
 * The most simple Invitation implementation
 */
class SimpleInvitation implements InvitationInterface
{
    /**
     * @var string
     */
    protected $code;

    /**
     * Set code
     *
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
