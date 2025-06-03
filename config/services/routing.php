<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Routing\Loader\ResourceRoutingLoader;
use LAG\AdminBundle\Routing\Route\RouteNameGenerator;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\ParametersMapper;
use LAG\AdminBundle\Routing\UrlGenerator\ParametersMapperInterface;
use LAG\AdminBundle\Routing\UrlGenerator\PathGenerator;
use LAG\AdminBundle\Routing\UrlGenerator\PathGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGenerator;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(ResourceRoutingLoader::class)
        ->args([
            '$applicationParameter' => param('lag_admin.application_parameter'),
            '$resourceParameter' => param('lag_admin.resource_parameter'),
            '$operationParameter' => param('lag_admin.operation_parameter'),
            '$pathGenerator' => service(PathGeneratorInterface::class),
            '$definitionFactory' => service('lag_admin.definition.factory'),
            '$resourceFactory' => service('lag_admin.resource.factory'),
        ])
        ->tag('routing.loader')
    ;

    $services->set(RouteNameGeneratorInterface::class, RouteNameGenerator::class)
        ->alias('lag_admin.routing.route_name_generator', RouteNameGeneratorInterface::class)
    ;

    $services->set(ResourceUrlGeneratorInterface::class, ResourceUrlGenerator::class)
        ->args([
            '$router' => service('router'),
            '$mapper' => service(ParametersMapperInterface::class),
            '$operationFactory' => service('lag_admin.operation.factory'),
        ])
        ->alias('lag_admin.routing.url_generator', ResourceUrlGeneratorInterface::class)
    ;
    $services->set(ParametersMapperInterface::class, ParametersMapper::class);

    $services->set(PathGeneratorInterface::class, PathGenerator::class);
};
