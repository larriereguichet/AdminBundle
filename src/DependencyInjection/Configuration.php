<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('lag_admin');
        $rootNode = $treeBuilder->getRootNode();

        $this->addMappingSection($rootNode);
        $this->addApplicationsSection($rootNode);
        $this->addUploadsSection($rootNode);

        $rootNode
            ->children()
                ->scalarNode('request_parameter')->defaultValue('_lag_operation')->end()
                ->scalarNode('date_format')->defaultValue('medium')->end()
                ->scalarNode('time_format')->defaultValue('short')->end()
                ->booleanNode('date_localization')->defaultValue(true)->end()
                ->booleanNode('filter_events')->defaultValue(true)->end()
                ->booleanNode('cache')->defaultValue(true)->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function addMappingSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('mapping')
                    ->addDefaultsIfNotSet()
                    ->children()
                    ->arrayNode('paths')
                        ->prototype('scalar')->end()
                        ->defaultValue(['%kernel.project_dir%/src/Entity'])->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addUploadsSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('uploads')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('storage')->defaultValue('lag_admin.media_storage')->end()
                        ->scalarNode('media_directory')->defaultValue('%kernel.project_dir%/public/admin/media/uploads')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addApplicationsSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('applications')
                    ->useAttributeAsKey('name', false)
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
            ->end()
        ;
    }
}
