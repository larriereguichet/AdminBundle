<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\KnpMenu\Extension\ResourceExtension;
use LAG\AdminBundle\Menu\Builder\ContextualMenuBuilder;
use LAG\AdminBundle\Menu\Builder\ResourceMenuBuilder;
use LAG\AdminBundle\Menu\Builder\UserMenuBuilder;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Menu builders
    $services->set(ContextualMenuBuilder::class)
        ->arg('$resourceContext', service(ResourceContextInterface::class))
        ->arg('$registry', service(ResourceRegistryInterface::class))
        ->arg('$requestStack', service('request_stack'))
        ->arg('$routeNameGenerator', service(RouteNameGeneratorInterface::class))
        ->arg('$factory', service('knp_menu.factory'))
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
        ->arg('$factory', service('knp_menu.factory'))
        ->arg('$resourceRegistry', service(ResourceRegistryInterface::class))
        ->arg('$routeNameGenerator', service(RouteNameGeneratorInterface::class))
        ->tag('knp_menu.menu_builder', ['method' => 'build', 'alias' => 'resource'])
    ;

    // KnpMenu bridge
    $services->set(ResourceExtension::class)
        ->arg('$registry', service(ResourceRegistryInterface::class))
        ->arg('$urlGenerator', service(UrlGeneratorInterface::class))
        ->tag('knp_menu.factory_extension')
    ;
};
