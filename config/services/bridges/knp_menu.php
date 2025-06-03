<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\KnpMenu\Extension\ResourceExtension;
use LAG\AdminBundle\Menu\Builder\ContextualMenuBuilder;
use LAG\AdminBundle\Menu\Builder\ResourceMenuBuilder;
use LAG\AdminBundle\Menu\Builder\UserMenuBuilder;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Menu builders
    $services->set(ContextualMenuBuilder::class)
        ->args([
            '$operationContext' => service('lag_admin.operation.context'),
            '$operationFactory' => service('lag_admin.operation.factory'),
            '$routeNameGenerator' => service('lag_admin.routing.route_name_generator'),
            '$factory' => service('knp_menu.factory'),
        ])
        ->tag('knp_menu.menu_builder', ['method' => 'build', 'alias' => 'contextual'])
    ;
    $services->set(UserMenuBuilder::class)
        ->arg('$factory', service('knp_menu.factory'))
        ->tag('knp_menu.menu_builder', [
            'method' => 'build',
            'alias' => 'user',
        ])
    ;
    $services->set(ResourceMenuBuilder::class)
        ->args([
            '$factory' => service('knp_menu.factory'),
            '$definitionFactory' => service('lag_admin.definition.factory'),
            '$resourceFactory' => service('lag_admin.resource.factory'),
            '$routeNameGenerator' => service('lag_admin.routing.route_name_generator'),
        ])
        ->tag('knp_menu.menu_builder', ['method' => 'build', 'alias' => 'resource'])
    ;

    // KnpMenu bridge
    $services->set(ResourceExtension::class)
        ->args([
            '$operationFactory' => service('lag_admin.operation.factory'),
            '$urlGenerator' => service('lag_admin.routing.url_generator'),
        ])
        ->tag('knp_menu.factory_extension')
    ;
};
