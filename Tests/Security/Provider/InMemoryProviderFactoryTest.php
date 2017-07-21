<?php

namespace Cethyworks\InvitationBundle\Tests\Security\Provider;

use Cethyworks\InvitationBundle\Security\Provider\InMemoryProviderFactory;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InMemoryProviderFactoryTest extends AbstractProviderFactoryTest
{
    protected $factoryClass = InMemoryProviderFactory::class;

    public function testCreate()
    {
        $providerId = 'provider_id';

        $config = [
            'invitations' => [
                ['code' => 'foo'],
                ['code' => 'bar'],
            ],
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

        $definition->expects($this->once())
            ->method('addArgument')
            ->with($config['invitations'])
            ->willReturn($definition);


        $factory = new $this->factoryClass('key', $providerId);

        $factory->create($container, $providerId, $config);
    }


    public function dataTestAddValidConfiguration()
    {
        return [
            'empty configuration'   => [
                [],
                [
                    'invitations' => []
                ]
            ],
            'partial configuration' => [
                [
                    'invitations' => [
                        ['code' => 'foo'],
                        ['code' => 'bar'],
                    ],
                ], [
                    'invitations' => [
                        ['code' => 'foo'],
                        ['code' => 'bar'],
                    ],
                ]
            ],
            'full configuration'    => [
                [
                    'invitations' => [
                        ['code' => 'foo', 'email' => 'foo@foo.com'],
                        ['code' => 'bar', 'email' => 'bar@bar.com'],
                    ],
                ], [
                    'invitations' => [
                        ['code' => 'foo', 'email' => 'foo@foo.com'],
                        ['code' => 'bar', 'email' => 'bar@bar.com'],
                    ],
                ]
            ],
        ];
    }

    public function dataTestAddInvalidConfiguration()
    {
        return [
            'missing (code) configuration' => [
                [
                    'invitations' => [
                        ['email' => 'foo@foo.com'],
                        ['email' => 'bar@bar.com'],
                    ],
                ]
            ],

            'too much elements configuration' => [
                [
                    'invitations' => [
                        ['code' => 'foo', 'email' => 'foo@foo.com', 'foo' => 'bar'],
                        ['code' => 'bar', 'email' => 'bar@bar.com'],
                    ],
                ],
            ],
        ];
    }
}
