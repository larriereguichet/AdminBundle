<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection;

use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
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
                ->scalarNode('resources_path')->defaultValue('%kernel.project_dir%/config/admin/resources')->end()
                ->scalarNode('admin_class')->defaultValue(Admin::class)->end()
                ->scalarNode('action_class')->defaultValue(Action::class)->end()

                // Templates
                ->scalarNode('base_template')->defaultValue('@LAGAdmin/base.html.twig')->end()
                ->scalarNode('ajax_template')->defaultValue('@LAGAdmin/empty.html.twig')->end()
                ->scalarNode('menu_template')->defaultValue('@LAGAdmin/menu/menu.html.twig')->end()
                ->scalarNode('create_template')->defaultValue('@LAGAdmin/crud/create.html.twig')->end()
                ->scalarNode('edit_template')->defaultValue('@LAGAdmin/crud/edit.html.twig')->end()
                ->scalarNode('list_template')->defaultValue('@LAGAdmin/crud/list.html.twig')->end()
                ->scalarNode('delete_template')->defaultValue('@LAGAdmin/crud/delete.html.twig')->end()

                // Routing
                ->scalarNode('routes_pattern')->defaultValue('lag_admin.{admin}.{action}')->end()
                ->scalarNode('homepage_route')->defaultValue('lag_admin.homepage')->end()

                // Dates
                ->scalarNode('date_format')->defaultValue('Y/m/d')->end()

                // Pagination
                ->variableNode('pager')->defaultValue('pagerfanta')->end()
                ->integerNode('max_per_page')->min(1)->defaultValue(25)->end()
                ->scalarNode('page_parameter')->defaultValue('page')->end()

                // List default parameters
                ->integerNode('string_length')->defaultValue(200)->end()
                ->scalarNode('string_truncate')->defaultValue('...')->end()

                // Default permissions
                ->scalarNode('permissions')->defaultValue('ROLE_ADMIN')->end()

                // Translation
                ->arrayNode('translation')
                    ->canBeEnabled()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('pattern')->defaultValue('admin.{admin}.{key}')->end()
                        ->scalarNode('catalog')->defaultValue('admin')->end()
                    ->end()
                ->end()

                // Fields default mapping
                ->arrayNode('fields_mapping')
                    ->prototype('scalar')->end()
                    ->defaultValue(ApplicationConfiguration::FIELD_MAPPING)
                ->end()

                // Menus
                ->variableNode('menus')
                    ->defaultValue([
                        'top' => null,
                        'left' => null,
                    ])
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
