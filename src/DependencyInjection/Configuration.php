<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\ORMProvider;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('lag_admin');
        $rootNode = $treeBuilder->getRootNode();

        $this->addRequestSection($rootNode);
        $this->addMappingSection($rootNode);
        $this->addApplicationsSection($rootNode);
        $this->addUploadsSection($rootNode);
        $this->addResourcesSection($rootNode);
        $this->addGridsSection($rootNode);

        $rootNode
            ->children()
                ->scalarNode('date_format')->defaultValue('medium')->end()
                ->scalarNode('time_format')->defaultValue('short')->end()
                ->scalarNode('media_directory')->defaultValue('media/images')->end()
                ->booleanNode('date_localization')->defaultValue(true)->end()
                ->booleanNode('filter_events')->defaultValue(true)->end()
                ->booleanNode('cache')->defaultValue(true)->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function addRequestSection(ArrayNodeDefinition $rootNode): void
    {
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
        ;
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
                    ->scalarNode('storage')->defaultValue('lag_admin_image.storage')->end()
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

    private function addResourcesSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('resources')
                    ->arrayPrototype()
                        ->addDefaultsIfNotSet()
                        ->ignoreExtraKeys(false)
                        ->children()
                            ->scalarNode('name')->isRequired()->end()
                            ->scalarNode('resource_class')->isRequired()->end()
                            ->scalarNode('application')->defaultNull()->end()
                            ->scalarNode('title')->defaultNull()->end()
                            ->scalarNode('group')->defaultNull()->end()
                            ->scalarNode('icon')->defaultNull()->end()

                            ->scalarNode('path_prefix')->defaultNull()->end()
                            ->scalarNode('route_pattern')->defaultValue('{application}.{resource}.{operation}')->end()

                            ->scalarNode('translation_pattern')->defaultNull()->end()
                            ->scalarNode('translation_domain')->defaultNull()->end()

                            ->scalarNode('form')->defaultNull()->end()
                            ->scalarNode('form_template')->defaultNull()->end()
                            ->arrayNode('form_options')->variablePrototype()->end()->end()

                            ->booleanNode('validation')->defaultTrue()->end()
                            ->arrayNode('validation_context')->variablePrototype()->end()->end()

                            ->booleanNode('ajax')->defaultTrue()->end()

                            ->scalarNode('provider')->defaultValue(ORMProvider::class)->end()
                            ->scalarNode('processor')->defaultValue(ORMProcessor::class)->end()

                            ->arrayNode('permissions')
                                ->scalarPrototype()->end()
                            ->end()

                            ->arrayNode('identifiers')->scalarPrototype()->defaultValue(['id'])->end()->end()

                            ->arrayNode('operations')
                                ->variablePrototype()->end()
                            ->end()

                            ->arrayNode('properties')
                                ->variablePrototype()->end()
                            ->end()

                            ->scalarNode('input')->defaultNull()->end()
                            ->scalarNode('output')->defaultNull()->end()

                            ->arrayNode('normalization_context')
                                ->variablePrototype()->end()
                            ->end()
                            ->arrayNode('denormalization_context')
                                ->variablePrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addGridsSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('grids')
                    ->arrayPrototype()
                    ->addDefaultsIfNotSet()
                    ->ignoreExtraKeys(false)
                    ->children()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('title')->end()
                        ->scalarNode('type')->defaultValue('table')->end()
                        ->scalarNode('template')->end()
                        ->scalarNode('component')->end()
                        ->scalarNode('translation_domain')->end()
                        ->scalarNode('header_template')->end()

                        ->arrayNode('properties')
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('attributes')
                            ->variablePrototype()->end()
                        ->end()
                        ->arrayNode('container_attributes')
                            ->variablePrototype()->end()
                        ->end()
                        ->arrayNode('action_cell_attributes')
                            ->variablePrototype()->end()
                        ->end()
                        ->arrayNode('header_row_attributes')
                            ->variablePrototype()->end()
                        ->end()
                        ->arrayNode('header_attributes')
                            ->variablePrototype()->end()
                        ->end()
                        ->arrayNode('options')
                            ->variablePrototype()->end()
                        ->end()

                        ->scalarNode('form')->end()
                        ->arrayNode('form_options')
                            ->variablePrototype()->end()
                        ->end()

                        ->arrayNode('actions')
                            ->variablePrototype()->end()
                        ->end()
                        ->arrayNode('collection_actions')
                            ->variablePrototype()->end()
                        ->end()

                        ->scalarNode('empty_message')->end()
                        ->booleanNode('sortable')->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('grids_templates')
                ->defaultValue([
                    'table' => '@LAGAdmin/grids/table.html.twig',
                    'card' => '@LAGAdmin/grids/card.html.twig',
                ])
                ->variablePrototype()->end()
            ->end()
        ;
    }
}
