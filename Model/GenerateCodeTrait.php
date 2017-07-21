<?php

namespace Cethyworks\InvitationBundle\Model;

trait GenerateCodeTrait
{
    /**
     * @return $this
     */
    protected function generateCode()
    {
        return $this->setCode(uniqid());
    }
}
