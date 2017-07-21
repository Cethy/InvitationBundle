<?php

namespace Cethyworks\InvitationBundle\DependencyInjection;

use Cethyworks\InvitationBundle\Model\Invitation;
use Cethyworks\InvitationBundle\Model\SimpleInvitation;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cethyworks_invitation');

        $rootNode
            ->children()
                ->scalarNode('invitation_class')
                    ->cannotBeEmpty()
                    ->defaultValue(SimpleInvitation::class)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
