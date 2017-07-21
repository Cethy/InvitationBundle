<?php

namespace Cethyworks\InvitationBundle\Tests\Security\Provider;

use Cethyworks\InvitationBundle\Model\Invitation;
use Cethyworks\InvitationBundle\Model\InvitedUser;
use Cethyworks\InvitationBundle\Model\SimpleInvitation;
use Cethyworks\InvitationBundle\Security\Provider\EntityProvider;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

class EntityProviderTest extends AbstractProviderTest
{
    protected $providerClass = EntityProvider::class;

    /**
     * @var EntityProvider
     */
    protected $provider;

    /**
     * @var ObjectRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * @var string
     */
    protected $repository_method = 'repository_method';

    /**
     * @var string
     */
    protected $entity_property = 'property';


    public function testInvalidInvitationClassGiven()
    {
        $this->setExpectedException(\LogicException::class, 'DateTime must implements the Cethyworks\InvitationBundle\Model\InvitationInterface interface.');

        new EntityProvider(\DateTime::class, $this->getRegistryMock(), 'findOneBy', 'code');
    }

    public function dataTestLoadUserByUsername()
    {
        return [
            'foo user' => ['foo'],
            'bar user' => ['bar']
        ];
    }
    /**
     * @dataProvider dataTestLoadUserByUsername
     */
    public function testLoadUserByUsername($code)
    {
        $this->setRepositoryReturnInvitedUser($code);
        parent::testLoadUserByUsername($code);
    }

    public function dataTestLoadUserByUsernameThrowsUsernameNotFoundException()
    {
        return [
            ['unknown']
        ];
    }
    /**
     * @dataProvider dataTestLoadUserByUsernameThrowsUsernameNotFoundException
     */
    public function testLoadUserByUsernameThrowsUsernameNotFoundException($code)
    {
        $this->setRepositoryReturnNull($code);
        parent::testLoadUserByUsernameThrowsUsernameNotFoundException($code);
    }

    public function dataTestRefreshUser()
    {
        return [
            'foo user' => ['foo'],
            'bar user' => ['bar']
        ];
    }
    /**
     * @dataProvider dataTestRefreshUser
     */
    public function testRefreshUser($code)
    {
        $this->setRepositoryReturnInvitedUser($code);
        parent::testRefreshUser($code);
    }

    public function dataTestRefreshUserThrowsUsernameNotFoundException()
    {
        return [
            ['unknown']
        ];
    }
    /**
     * @dataProvider dataTestRefreshUserThrowsUsernameNotFoundException
     */
    public function testRefreshUserThrowsUsernameNotFoundException($code)
    {
        $this->setRepositoryReturnNull($code);
        parent::testRefreshUserThrowsUsernameNotFoundException($code);
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
        $invitations = [
            ['code' => 'foo'],
            ['code' => 'bar'],
        ];

        $managerName = 'manager_name';

        $this->repository = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->setMethods([$this->repository_method])
            ->getMockForAbstractClass();

        $objectManager = $this->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager->expects($this->any())
            ->method('getRepository')
            ->with(Invitation::class)
            ->willReturn($this->repository);

        $registry = $this->getRegistryMock();

        $registry->expects($this->any())
            ->method('getManager')
            ->with($managerName)
            ->willReturn($objectManager);

        $this->provider = new EntityProvider(Invitation::class, $registry, $this->repository_method, $this->entity_property, $managerName);
    }

    /**
     * @return ManagerRegistry|\PHPUnit_Framework_MockObject_MockObject $registry
     */
    protected function getRegistryMock()
    {
        return $this->getMockBuilder(ManagerRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();

    }

    /**
     * @param string $code
     */
    protected function setRepositoryReturnInvitedUser($code)
    {
        $invitation = new Invitation();
        $invitation->setCode($code);

        $this->repository->expects($this->once())
            ->method($this->repository_method)
            ->with([$this->entity_property => $code])
            ->willReturn($invitation);
    }

    /**
     * @param $code
     */
    protected function setRepositoryReturnNull($code)
    {
        $this->repository->expects($this->once())
            ->method($this->repository_method)
            ->with([$this->entity_property => $code])
            ->willReturn(null);
    }
}
