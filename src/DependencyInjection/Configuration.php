<?php

namespace LAG\AdminBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
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
                ->append($this->getMenuConfiguration())
            ->end()
        ->end();

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    protected function getAdminsConfigurationNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('admins');

        $node
            // useAttributeAsKey() method will preserve keys when multiple configurations files are used and then avoid
            // admin not found by configuration override
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('entity')->end()
                    ->scalarNode('data_provider')->end()
                    ->scalarNode('form')->end()
                    ->scalarNode('max_per_page')->end()
                    ->scalarNode('controller')->defaultValue('LAGAdminBundle:CRUD')->end()
                    // actions configurations
                    ->variableNode('actions')->end()
                ->end()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    protected function getApplicationNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('application');

        $node
            ->children()
                ->scalarNode('title')->end()
                ->scalarNode('description')->end()
                ->scalarNode('base_template')->end()
                ->scalarNode('date_format')->defaultValue('Y-m-d')->end()
                ->scalarNode('bootstrap')->end()
                ->scalarNode('max_per_page')->end()
                ->scalarNode('routing_name_pattern')->end()
                ->scalarNode('routing_url_pattern')->end()
                ->booleanNode('translation')->end()
                ->scalarNode('translation_pattern')->end()
                ->scalarNode('block_template')
                    ->defaultValue('LAGAdminBundle:Form:fields.html.twig')
                ->end()
                ->arrayNode('fields_mapping')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->booleanNode('enable_extra_configuration')->end()
                ->booleanNode('enable_security')->end()
                ->booleanNode('enable_menus')->end()
                ->booleanNode('enable_homepage')->end()
                ->booleanNode('translation')->end()
                ->variableNode('translation_pattern')->end()
                ->scalarNode('title')->end()
                ->scalarNode('description')->end()
                ->scalarNode('locale')->end()
                ->scalarNode('base_template')->end()
                ->scalarNode('block_template')->end()
                ->scalarNode('menu_template')->end()
                ->scalarNode('homepage_template')->end()
                ->scalarNode('homepage_route')->end()
                ->scalarNode('routing_url_pattern')->end()
                ->scalarNode('routing_name_pattern')->end()
                ->scalarNode('bootstrap')->end()
                ->scalarNode('date_format')->end()
                ->scalarNode('pager')->end()
                ->scalarNode('string_length')->end()
                ->scalarNode('string_length_truncate')->end()
                ->scalarNode('max_per_page')->end()
                ->scalarNode('admin_class')->end()
                ->scalarNode('action_class')->end()
                ->scalarNode('permissions')->end()
                ->scalarNode('page_parameter')->end()
            ->end()
        ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    protected function getMenuConfiguration()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('menus');

        $node
            ->prototype('variable')
            ->end()
        ->end();

        return $node;
    }
}
