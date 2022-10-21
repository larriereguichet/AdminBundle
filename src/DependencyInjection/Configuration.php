<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('lag_admin');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('title')->defaultValue('Admin')->end()
                ->scalarNode('description')->defaultValue('Admin')->end()
                ->scalarNode('translation_domain')->defaultValue('admin')->end()

                ->arrayNode('resource_paths')
                    ->prototype('scalar')->end()
                    ->defaultValue([
                        '%kernel.project_dir%/config/admin/resources',
                        '%kernel.project_dir%/src/Entity',
                    ])
                ->end()

                ->scalarNode('date_format')->defaultValue('medium')->end()
                ->scalarNode('time_format')->defaultValue('short')->end()
                ->booleanNode('date_localization')->defaultValue(true)->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
