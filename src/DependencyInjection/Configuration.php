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
                ->arrayNode('request')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('application_parameter')->defaultValue('_application')->end()
                        ->scalarNode('resource_parameter')->defaultValue('_resource')->end()
                        ->scalarNode('operation_parameter')->defaultValue('_operation')->end()
                    ->end()
                ->end()

                ->scalarNode('default_application')->defaultValue('admin')->end()
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

                ->arrayNode('grid_paths')
                    ->prototype('scalar')->end()
                    ->defaultValue([
                        '%kernel.project_dir%/config/admin/grids',
                        '%kernel.project_dir%/src/Entity',
                    ])
                ->end()

                ->arrayNode('uploads')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('storage')->defaultValue('lag_admin_image.storage')->end()
                    ->end()
                ->end()

                ->scalarNode('date_format')->defaultValue('medium')->end()
                ->scalarNode('time_format')->defaultValue('short')->end()
                ->scalarNode('media_directory')->defaultValue('media/images')->end()
                ->booleanNode('date_localization')->defaultValue(true)->end()
                ->booleanNode('filter_events')->defaultValue(true)->end()

                ->arrayNode('applications')
                ->useAttributeAsKey('name')
                ->arrayPrototype()
                    ->children()
                    ->scalarNode('name')->end()
                    ->scalarNode('date_format')->defaultValue('medium')->end()
                    ->scalarNode('time_format')->defaultValue('short')->end()
                    ->scalarNode('translation_domain')->end()
                    ->scalarNode('translation_pattern')->defaultValue('{application}.{resource}.{message}')->end()
                    ->scalarNode('route_pattern')->defaultValue('{application}.{resource}.{operation}')->end()
                    ->scalarNode('base_template')->defaultValue('@LAGAdmin/base.html.twig')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
