<?php

namespace BlueBear\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('blue_bear_admin');

        $rootNode
            ->children()
                ->arrayNode('application')
                    ->children()
                        ->scalarNode('title')->end()
                        ->scalarNode('description')->end()
                        ->scalarNode('layout')->end()
                        ->scalarNode('max_per_page')->end()
                        ->scalarNode('block_template')
                            ->defaultValue('BlueBearAdminBundle:Form:fields.html.twig')
                        ->end()
                    ->end()
                ->end()
                // admins configuration
                ->arrayNode('admins')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('entity')->end()
                            ->scalarNode('form')->end()
                            ->scalarNode('max_per_page')->end()
                            ->scalarNode('controller')->defaultValue('BlueBearAdminBundle:Generic')->end()
                            ->arrayNode('manager')
                                ->children()
                                    ->scalarNode('name')->end()
                                    ->scalarNode('save')->end()
                                ->end()
                            ->end()
                            ->arrayNode('actions')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('title')->end()
                                        ->arrayNode('permissions')
                                            ->defaultValue(['ROLE_USER'])
                                            ->prototype('scalar')
                                            ->end()
                                        ->end()
                                        ->arrayNode('fields')
                                                ->prototype('array')
                                                ->children()
                                                    ->scalarNode('length')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                // menus configurations
                ->arrayNode('menus')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('template')->defaultValue('BlueBearAdminBundle:Menu:main_menu.html.twig') ->end()
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
}
