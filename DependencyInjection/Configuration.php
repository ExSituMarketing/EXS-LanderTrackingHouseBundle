<?php

namespace EXS\LanderTrackingHouseBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('exs_lander_tracking_house');

        $rootNode
            ->children()
                ->scalarNode('default_cmp')
                    ->defaultValue(1)
                ->end()
                ->scalarNode('default_exid')
                    ->defaultValue('exid')
                ->end()
                ->scalarNode('default_visit')
                    ->defaultValue(1)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
