<?php

namespace Cethyworks\InvitationBundle\Tests\Model;

use Cethyworks\InvitationBundle\Model\GenerateCodeTrait;
use Cethyworks\InvitationBundle\Model\SimpleInvitation;
use PHPUnit\Framework\TestCase;

class GenerateCodeTraitTest extends TestCase
{
    public function testGenerateUniqueCode()
    {
        $invitation = new MockInvitation();
        $invitation2 = new MockInvitation();

        $this->assertNotEmpty($invitation->getCode());
        $this->assertNotEmpty($invitation2->getCode());
        $this->assertNotEquals($invitation->getCode(), $invitation2->getCode());
    }
}

class MockInvitation extends SimpleInvitation
{
    use GenerateCodeTrait;

    function __construct()
    {
        $this->generateCode();
    }
}
