<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Loader\ResourceRoutingLoader;
use LAG\AdminBundle\Routing\Route\RouteNameGenerator;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\ParametersMapper;
use LAG\AdminBundle\Routing\UrlGenerator\ParametersMapperInterface;
use LAG\AdminBundle\Routing\UrlGenerator\PathGenerator;
use LAG\AdminBundle\Routing\UrlGenerator\PathGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGenerator;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(ResourceRoutingLoader::class)
        ->arg('$applicationParameter', param('lag_admin.application_parameter'))
        ->arg('$resourceParameter', param('lag_admin.resource_parameter'))
        ->arg('$operationParameter', param('lag_admin.operation_parameter'))
        ->arg('$pathGenerator', service(PathGeneratorInterface::class))
        ->arg('$resourceRegistry', service(ResourceRegistryInterface::class))
        ->tag('routing.loader')
    ;

    $services->set(RouteNameGeneratorInterface::class, RouteNameGenerator::class)
        ->alias('lag_admin.routing.route_name_generator', RouteNameGeneratorInterface::class)
    ;

    $services->set(UrlGeneratorInterface::class, UrlGenerator::class)
        ->arg('$router', service('router'))
        ->arg('$mapper', service(ParametersMapperInterface::class))
        ->arg('$resourceRegistry', service(ResourceRegistryInterface::class))

        ->alias('lag_admin.routing.url_generator', UrlGeneratorInterface::class)
    ;
    $services->set(ParametersMapperInterface::class, ParametersMapper::class);

    $services->set(PathGeneratorInterface::class, PathGenerator::class);
};
