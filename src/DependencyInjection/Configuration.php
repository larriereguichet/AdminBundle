<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection;

use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Metadata\Operation;
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

                // Where the Admin resources file are located
                ->arrayNode('resource_paths')
                    ->prototype('scalar')->end()
                    ->defaultValue([
                        '%kernel.project_dir%/config/admin/resources',
                        '%kernel.project_dir%/src/Entity',
                    ])
                ->end()

                // Translation
                ->arrayNode('translation')
                    ->canBeEnabled()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('pattern')->defaultValue('admin.{admin}.{key}')->end()
                        ->scalarNode('domain')->defaultValue('admin')->end()
                    ->end()
                ->end()

                // Fields default mapping
                ->arrayNode('fields_mapping')
                    ->prototype('scalar')->end()
                    ->defaultValue(ApplicationConfiguration::FIELD_MAPPING)
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
