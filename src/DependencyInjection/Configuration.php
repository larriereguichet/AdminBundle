<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection;

use LAG\AdminBundle\Metadata\Property\Image;
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
                ->scalarNode('name')->defaultValue('lag_admin')->end()
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
                ->booleanNode('filter_events')->defaultValue(true)->end()

                ->arrayNode('grids')
                    ->defaultValue([
                        'table' => [
                            'template' => '@LAGAdmin/grids/table_grid.html.twig',
                        ],
                        'card' => [
                            'template' => '@LAGAdmin/grids/card_grid.html.twig',
                            'template_mapping' => [
                                Image::class => '@LAGAdmin/grids/cards/card_image.html.twig'
                            ],
                        ],
                    ])
                    ->arrayPrototype()
                        ->useAttributeAsKey('name')
                        ->arrayPrototype()
                            ->children()
                            ->scalarNode('name')->end()
                            ->scalarNode('template')->end()
                            ->arrayNode('options')
                                ->arrayPrototype()->end()
                            ->end()
                            ->arrayNode('template_mapping')
                                ->arrayPrototype()->end()
                                ->defaultValue([])
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
