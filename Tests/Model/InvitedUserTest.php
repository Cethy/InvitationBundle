<?php

namespace Cethyworks\InvitationBundle\Tests\Model;

use Cethyworks\InvitationBundle\Model\InvitedUser;
use Cethyworks\InvitationBundle\Model\SimpleInvitation;
use PHPUnit\Framework\TestCase;

class InvitedUserTest extends TestCase
{
    public function testGetRoles()
    {
        $user = new InvitedUser(new SimpleInvitation());

        $this->assertEquals([InvitedUser::ROLE_INVITED], $user->getRoles());
    }

    public function testGetPassword()
    {
        $user = new InvitedUser(new SimpleInvitation());

        $this->assertEquals('', $user->getPassword());
        $this->assertNull($user->getSalt());
    }
}
