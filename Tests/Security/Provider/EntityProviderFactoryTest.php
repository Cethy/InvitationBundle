<?php

namespace Cethyworks\InvitationBundle\Tests\Security\Provider;

use Cethyworks\InvitationBundle\Security\Provider\EntityProviderFactory;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EntityProviderFactoryTest extends AbstractProviderFactoryTest
{
    protected $factoryClass = EntityProviderFactory::class;

    public function testCreate()
    {
        $providerId = 'provider_id';

        $config = [
            'repository_method' => 'fooo',
            'entity_property'   => 'bazz',
            'manager_name'      => 'barr'
        ];

        $container = $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $definition = $this->getMockBuilder(ChildDefinition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects($this->once())
            ->method('setDefinition')
            ->willReturn($definition);

        $definition->expects($this->at(0))
            ->method('addArgument')
            ->with($config['repository_method'])
            ->willReturn($definition);

        $definition->expects($this->at(1))
            ->method('addArgument')
            ->with($config['entity_property'])
            ->willReturn($definition);

        $definition->expects($this->at(2))
            ->method('addArgument')
            ->with($config['manager_name'])
            ->willReturn($definition);

        /** @var EntityProviderFactory $factory */
        $factory = new $this->factoryClass('key', $providerId);

        $factory->create($container, $providerId, $config);
    }


    public function dataTestAddValidConfiguration()
    {
        return [
            'empty configuration'   => [
                [],
                [
                    'repository_method' => 'findOneBy',
                    'entity_property'   => 'code',
                    'manager_name'      => null,
                ]
            ],
            'partial configuration' => [
                [
                    'entity_property' => 'foo',
                ], [
                    'repository_method' => 'findOneBy',
                    'entity_property'   => 'foo',
                    'manager_name'      => null,
                ]
            ],
            'full configuration'    => [
                [
                    'repository_method' => 'bar',
                    'entity_property'   => 'foo',
                    'manager_name'      => 'baz',
                ], [
                    'repository_method' => 'bar',
                    'entity_property'   => 'foo',
                    'manager_name'      => 'baz',
                ]
            ],
        ];
    }

    public function dataTestAddInvalidConfiguration()
    {
        return [
            'too much configuration' => [
                [
                    'entity_property' => 'baz',
                    'foo'             => 'foo'
                ]
            ],
        ];
    }
}
