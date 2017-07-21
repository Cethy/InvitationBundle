<?php

namespace Cethyworks\InvitationBundle\Security\Provider;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class InMemoryProviderFactory implements UserProviderFactoryInterface
{
    private $key;
    private $providerId;

    public function __construct($key, $providerId)
    {
        $this->key        = $key;
        $this->providerId = $providerId;
    }

    public function create(ContainerBuilder $container, $id, $config)
    {
        $container
            ->setDefinition($id, new ChildDefinition($this->providerId))
            ->addArgument($config['invitations'])
        ;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('invitations')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('code')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode('email')
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
