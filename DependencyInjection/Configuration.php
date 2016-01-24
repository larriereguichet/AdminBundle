<?php

namespace LAG\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lag_admin');

        $rootNode
            ->children()
                // application configuration
                ->append($this->getApplicationNode())
                // admins configuration
                ->append($this->getAdminsConfigurationNode())
                // menus configurations
                ->arrayNode('menus')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('template')->defaultValue('LAGAdminBundle:Menu:main_menu.html.twig')->end()
                            ->arrayNode('main_item')
                                ->children()
                                    ->scalarNode('route')->end()
                                    ->scalarNode('title')->end()
                                ->end()
                            ->end()
                            ->arrayNode('items')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('admin')->end()
                                        ->scalarNode('action')->end()
                                        ->scalarNode('title')->end()
                                        ->arrayNode('permissions')
                                            ->defaultValue(['ROLE_USER'])
                                            ->prototype('scalar')
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    public function getAdminsConfigurationNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('admins');

        $node
            ->prototype('array')
                ->children()
                    ->scalarNode('entity')->end()
                    ->scalarNode('data_provider')->end()
                    ->scalarNode('form')->end()
                    ->scalarNode('max_per_page')->end()
                    ->scalarNode('controller')->defaultValue('LAGAdminBundle:CRUD')->end()
                    // actions configurations
                    ->arrayNode('actions')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('title')->end()
                                ->arrayNode('permissions')
                                    ->defaultValue(['ROLE_USER'])
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('export')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('order')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('field')->end()
                                            ->scalarNode('order')->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('fields')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('type')->end()
                                            ->arrayNode('options')
                                                ->prototype('variable')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('filters')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('batch')
                                    ->prototype('variable')->end()
                                ->end()
                                ->arrayNode('actions')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('title')->end()
                                            ->scalarNode('route')->end()
                                            ->scalarNode('target')->end()
                                            ->scalarNode('icon')->end()
                                            ->scalarNode('load_strategy')->end()
                                            ->arrayNode('criteria')
                                                ->prototype('array')
                                                ->end()
                                            ->end()
                                            ->arrayNode('parameters')
                                                ->prototype('array')
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    public function getApplicationNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('application');

        $node
            ->children()
                ->scalarNode('title')->end()
                ->scalarNode('description')->end()
                ->scalarNode('layout')->end()
                ->scalarNode('date_format')->defaultValue('Y-m-d')->end()
                ->scalarNode('bootstrap')->end()
                ->scalarNode('max_per_page')->end()
                ->arrayNode('routing')
                    ->children()
                        ->scalarNode('name_pattern')->end()
                        ->scalarNode('url_pattern')->end()
                    ->end()
                ->end()
                ->arrayNode('translation')
                    ->canBeDisabled()
                    ->children()
                        ->scalarNode('pattern')->end()
                    ->end()
                ->end()
                ->scalarNode('block_template')
                ->defaultValue('LAGAdminBundle:Form:fields.html.twig')
                ->end()
                ->arrayNode('fields_mapping')
                ->prototype('scalar')
                ->end()
                ->end()
            ->end()
        ->end();

        return $node;
    }
}
