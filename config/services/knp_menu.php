<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\KnpMenu\Builder\ContextualMenuBuilder;
use LAG\AdminBundle\Bridge\KnpMenu\Builder\ResourceMenuBuilder;
use LAG\AdminBundle\Bridge\KnpMenu\Builder\UserMenuBuilder;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(ContextualMenuBuilder::class)
        ->arg('$parametersExtractor', service(ParametersExtractorInterface::class))
        ->arg('$requestStack', service('request_stack'))
        ->arg('$registry', service(ResourceRegistryInterface::class))
        ->arg('$factory', service('knp_menu.factory'))
        ->arg('$routeNameGenerator', service(RouteNameGeneratorInterface::class))
        ->arg('$eventDispatcher', service('event_dispatcher'))
        ->tag('knp_menu.menu_builder', [
            'method' => 'build',
            'alias' => 'contextual',
        ])
    ;

    $services->set(UserMenuBuilder::class)
        ->arg('$factory', service('knp_menu.factory'))
        ->arg('$eventDispatcher', service('event_dispatcher'))
        ->tag('knp_menu.menu_builder', [
            'method' => 'build',
            'alias' => 'user',
        ])
    ;

    $services->set(ResourceMenuBuilder::class)
        ->arg('$factory', service('knp_menu.factory'))
        ->arg('$resourceRegistry', service(ResourceRegistryInterface::class))
        ->arg('$routeNameGenerator', service(RouteNameGeneratorInterface::class))
        ->arg('$eventDispatcher', service('event_dispatcher'))
        ->tag('knp_menu.menu_builder', [
            'method' => 'build',
            'alias' => 'resource',
        ])
    ;
};
