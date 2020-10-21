<?php

namespace LAG\AdminBundle\DependencyInjection;

use LAG\AdminBundle\Controller\AdminAction;
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
        $treeBuilder = new TreeBuilder('lag_admin');
        $rootNode = $treeBuilder->getRootNode();

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
     * This method preserves the compatibility with Symfony 3.4.
     */
    protected function createRootNode(string $name): NodeDefinition
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder($name);

            return $treeBuilder->getRootNode();
        } else {
            $treeBuilder = new TreeBuilder($name);

            return $treeBuilder->getRootNode();
        }
    }

    /**
     * @return ArrayNodeDefinition|NodeDefinition
     */
    protected function getAdminsConfigurationNode()
    {
        $node = $this->createRootNode('admins');
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
                    ->scalarNode('controller')->defaultValue(AdminAction::class)->end()
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
        $node = $this->createRootNode('application');

        $node
            ->children()
                ->scalarNode('title')->end()
                ->scalarNode('description')->end()
                ->scalarNode('resources_path')->defaultValue('%kernel.project_dir%/config/admin/resources')->end()
                ->scalarNode('base_template')->end()
                ->scalarNode('menu_template')->end()
                ->scalarNode('list_template')->end()
                ->scalarNode('block_template')
                    ->defaultValue('LAGAdminBundle:Form:fields.html.twig')
                ->end()
                ->scalarNode('date_format')->defaultValue('Y-m-d')->end()
                ->scalarNode('bootstrap')->end()
                ->scalarNode('max_per_page')->end()
                ->scalarNode('routing_name_pattern')->end()
                ->scalarNode('routing_url_pattern')->end()
                ->arrayNode('translation')
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('pattern')->defaultValue('admin.{admin}.{key}')->end()
                        ->scalarNode('catalog')->defaultValue('messages')->end()
                    ->end()
                ->end()
                ->arrayNode('fields_mapping')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->booleanNode('enable_extra_configuration')->defaultTrue()->end()
                ->booleanNode('enable_security')->end()
                ->booleanNode('enable_menus')->defaultTrue()->end()
                ->booleanNode('enable_homepage')->end()
                ->scalarNode('locale')->end()
                ->scalarNode('homepage_template')->end()
                ->scalarNode('homepage_route')->end()
                ->scalarNode('routing_url_pattern')->end()
                ->scalarNode('routing_name_pattern')->end()
                ->scalarNode('bootstrap')->end()
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
        $node = $this->createRootNode('menus');

        $node
            ->prototype('variable')
            ->end()
        ->end();

        return $node;
    }
}
