<?php

namespace Cethyworks\InvitationBundle\Tests\Model;

use Cethyworks\InvitationBundle\Model\Invitation;
use PHPUnit\Framework\TestCase;

class InvitationTest extends TestCase
{
    public function testEmail()
    {
        $invitation = new Invitation();

        $invitation->setEmail('some@email.com');

        $this->assertEquals('some@email.com', $invitation->getEmail());
    }
}
