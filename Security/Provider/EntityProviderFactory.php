<?php

namespace Cethyworks\InvitationBundle\Security\Provider;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EntityProviderFactory implements UserProviderFactoryInterface
{
    private $key;
    private $providerId;

    public function __construct($key, $providerId)
    {
        $this->key = $key;
        $this->providerId = $providerId;
    }

    public function create(ContainerBuilder $container, $id, $config)
    {
        $container
            ->setDefinition($id, new ChildDefinition($this->providerId))
                ->addArgument($config['repository_method'])
                ->addArgument($config['entity_property'])
                ->addArgument($config['manager_name'])
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
                ->scalarNode('repository_method')
                    ->cannotBeEmpty()
                    ->defaultValue('findOneBy')
                ->end()
                ->scalarNode('entity_property')
                    ->cannotBeEmpty()
                    ->defaultValue('code')
                ->end()
                ->scalarNode('manager_name')
                    ->defaultNull()
                ->end()
            ->end()
        ;
    }
}
