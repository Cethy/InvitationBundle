<?php

namespace Cethyworks\InvitationBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use Cethyworks\InvitationBundle\Security\InvitationAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class InvitationAuthenticatorTest extends TestCase
{
    /**
     * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $router;

    /**
     * @var InvitationAuthenticator
     */
    private $authenticator;


    public function testStart()
    {
        $response = $this->authenticator->start(new Request(), new AuthenticationException());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }


    public function dataTestGetCredentials()
    {
        return [
            'no credentials'    => [['foo' => 'bar']                        , null],
            'empty credentials' => [['foo' => 'bar', 'invitation' => '']    , ''],
            'some credentials'  => [['foo' => 'bar', 'invitation' => 'some'], 'some'],
        ];
    }

    /**
     * @dataProvider dataTestGetCredentials
     */
    public function testGetCredentials(array $queryParameters, $expectedCredentials)
    {
        $request = new Request($queryParameters);

        $credentials = $this->authenticator->getCredentials($request);

        $this->assertEquals($expectedCredentials, $credentials);
    }

    public function dataTestGetUser()
    {
        return [
            'no credentials'    => [null,   0],
            'empty credentials' => [''  ,   1],
            'some credentials'  => ['some', 1],
        ];
    }

    /**
     * @dataProvider dataTestGetUser
     */
    public function testGetUser($credentials, $userProviderCall)
    {
        /** @var UserInterface|\PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var UserProviderInterface|\PHPUnit_Framework_MockObject_MockObject $userProvider */
        $userProvider = $this->getMockBuilder(UserProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userProvider->expects($userProviderCall ? $this->once() : $this->never())
            ->method('loadUserByUsername')
            ->with($credentials)
            ->willReturn($user);

        if($userProviderCall) {
            $this->assertEquals($user, $this->authenticator->getUser($credentials, $userProvider));
        }
        else {
            $this->assertNull($this->authenticator->getUser($credentials, $userProvider));
        }
    }

    public function testCheckCredentials()
    {
        /** @var UserInterface|\PHPUnit_Framework_MockObject_MockObject $user */
        $user = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertTrue($this->authenticator->checkCredentials('', $user));
    }

    public function testOnAuthenticationFailure()
    {
        $response = $this->authenticator->onAuthenticationFailure(new Request(), new AuthenticationException());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function testOnAuthenticationSuccess()
    {
        $expectedUrl = 'https://success';
        $route       = 'foo';
        $routeParams = ['bar' => 'baz'];

        $this->router->expects($this->once())
            ->method('generate')
            ->with($route, $routeParams)
            ->willReturn($expectedUrl);

        /** @var TokenInterface $token */
        $token = $this->getMockBuilder(TokenInterface::class)
            ->getMock();

        /** @var RedirectResponse $response */
        $response = $this->authenticator->onAuthenticationSuccess(new Request([], [], [
            '_route' => $route,
            '_route_params' => $routeParams
        ]), $token, 'foo');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNotContains('?invitation=', $response->getTargetUrl());
    }

    public function testOnAuthenticationSuccessWithNoRoute()
    {
        $this->router->expects($this->never())
            ->method('generate');

        /** @var TokenInterface $token */
        $token = $this->getMockBuilder(TokenInterface::class)
            ->getMock();

        /** @var RedirectResponse $response */
        $response = $this->authenticator->onAuthenticationSuccess(new Request(), $token, 'foo');

        $this->assertNull($response);
    }

    public function testRememberMe()
    {
        $doesNotSupport = $this->authenticator->supportsRememberMe();

        $this->assertFalse($doesNotSupport);
    }


    protected function setUp()
    {
        $this->router = $this->getMockBuilder(RouterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->authenticator = new InvitationAuthenticator($this->router);
    }
}
