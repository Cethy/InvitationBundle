<?php

namespace Cethyworks\InvitationBundle\Tests\Security\Provider;

use Cethyworks\InvitationBundle\Model\InvitedUser;
use Cethyworks\InvitationBundle\Model\SimpleInvitation;
use Cethyworks\InvitationBundle\Security\Provider\AbstractUserInvitedProvider;
use Cethyworks\InvitationBundle\Security\Provider\InMemoryProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

abstract class AbstractProviderTest extends TestCase
{
    /**
     * @var string
     */
    protected $providerClass;

    /**
     * @var AbstractUserInvitedProvider
     */
    protected $provider;

    abstract public function dataTestLoadUserByUsername();
    abstract public function dataTestLoadUserByUsernameThrowsUsernameNotFoundException();
    abstract public function dataTestRefreshUser();
    abstract public function dataTestRefreshUserThrowsUsernameNotFoundException();
    abstract public function dataTestRefreshUserThrowsUnsupportedUserException();

    abstract public function dataTestSupportsClass();

    /**
     * @dataProvider dataTestLoadUserByUsername
     */
    public function testLoadUserByUsername($code)
    {
        /** @var InvitedUser $user */
        $user = $this->provider->loadUserByUsername($code);

        $this->assertInstanceOf(InvitedUser::class, $user);
        $this->assertInstanceOf(SimpleInvitation::class, $user->getInvitation());
        $this->assertEquals($code, $user->getInvitation()->getCode());
    }

    /**
     * @dataProvider dataTestLoadUserByUsernameThrowsUsernameNotFoundException
     */
    public function testLoadUserByUsernameThrowsUsernameNotFoundException($code)
    {
        $this->setExpectedException(UsernameNotFoundException::class, sprintf('Invitation code "%s" does not exist.', $code));

        $this->provider->loadUserByUsername($code);
    }

    /**
     * @dataProvider dataTestRefreshUser
     */
    public function testRefreshUser($code)
    {
        $invitation = new SimpleInvitation();
        $invitation->setCode($code);

        /** @var InvitedUser $user */
        $user = $this->provider->refreshUser(new InvitedUser($invitation));

        $this->assertInstanceOf(InvitedUser::class, $user);
        $this->assertInstanceOf(SimpleInvitation::class, $user->getInvitation());
        $this->assertEquals($code, $user->getInvitation()->getCode());
    }

    /**
     * @dataProvider dataTestRefreshUserThrowsUsernameNotFoundException
     */
    public function testRefreshUserThrowsUsernameNotFoundException($code)
    {
        $this->setExpectedException(UsernameNotFoundException::class, sprintf('Invitation code "%s" does not exist.', $code));

        $invitation = new SimpleInvitation();
        $invitation->setCode($code);

        $this->provider->refreshUser(new InvitedUser($invitation));
    }

    /**
     * @dataProvider dataTestRefreshUserThrowsUnsupportedUserException
     */
    public function testRefreshUserThrowsUnsupportedUserException($instance)
    {
        $this->setExpectedException(UnsupportedUserException::class, sprintf('Instances of "%s" are not supported.', get_class($instance)));

        $this->provider->refreshUser($instance);
    }

    /**
     * @dataProvider dataTestSupportsClass
     */
    public function testSupportsClass($class, $expectedSupport)
    {
        $provider = new InMemoryProvider(SimpleInvitation::class);

        $this->assertEquals($expectedSupport, $provider->supportsClass($class));
    }
}

class MockInvitedUser extends InvitedUser
{}
