<?php

namespace Cethyworks\InvitationBundle\Tests\Security\Provider;

use Cethyworks\InvitationBundle\Model\Invitation;
use Cethyworks\InvitationBundle\Model\InvitedUser;
use Cethyworks\InvitationBundle\Model\SimpleInvitation;
use Cethyworks\InvitationBundle\Security\Provider\InMemoryProvider;

class InMemoryProviderTest extends AbstractProviderTest
{
    protected $providerClass = InMemoryProvider::class;

    /**
     * @var InMemoryProvider
     */
    protected $provider;


    public function testInvalidInvitationClassGiven()
    {
        $this->setExpectedException(\LogicException::class, 'DateTime must implements the Cethyworks\InvitationBundle\Model\InvitationInterface interface.');

        new InMemoryProvider(\DateTime::class);
    }

    public function dataTestLoadUserByUsername()
    {
        return [
            'foo user' => ['foo'],
            'bar user' => ['bar']
        ];
    }

    public function dataTestLoadUserByUsernameThrowsUsernameNotFoundException()
    {
        return [
            ['unknown']
        ];
    }

    public function dataTestRefreshUser()
    {
        return [
            'foo user' => ['foo'],
            'bar user' => ['bar']
        ];
    }

    public function dataTestRefreshUserThrowsUsernameNotFoundException()
    {
        return [
            ['unknown']
        ];
    }

    public function dataTestRefreshUserThrowsUnsupportedUserException()
    {
        return [
            [new MockInvitedUser(new SimpleInvitation())]
        ];
    }

    public function dataTestSupportsClass()
    {
        return [
            'supported'                 => [InvitedUser::class, true],
            'not supported'             => [\DateTime::class, false],
            'inheritance not supported' => [MockInvitedUser::class, false],
        ];
    }

    protected function setUp()
    {
        $this->provider = new InMemoryProvider(Invitation::class, [
            ['code' => 'foo', 'email' => 'some@email.com'],
            ['code' => 'bar'],
        ]);
    }
}
