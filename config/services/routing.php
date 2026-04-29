<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Routing\Loader\ResourceRoutingLoader;
use LAG\AdminBundle\Routing\Route\RouteNameGenerator;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\LinkUrlGenerator;
use LAG\AdminBundle\Routing\UrlGenerator\LinkUrlGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\OperationUrlGenerator;
use LAG\AdminBundle\Routing\UrlGenerator\OperationUrlGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\ParametersMapper;
use LAG\AdminBundle\Routing\UrlGenerator\ParametersMapperInterface;
use LAG\AdminBundle\Routing\UrlGenerator\PathGenerator;
use LAG\AdminBundle\Routing\UrlGenerator\PathGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGenerator;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(ResourceRoutingLoader::class)
        ->args([
            '$requestParameter' => param('lag_admin.request_parameter'),
            '$pathGenerator' => service(PathGeneratorInterface::class),
            '$resourceCollectionFactory' => service('lag_admin.resource.collection_factory'),
        ])
        ->tag('routing.loader')
    ;

    $services->set(RouteNameGeneratorInterface::class, RouteNameGenerator::class)
        ->alias('lag_admin.routing.route_name_generator', RouteNameGeneratorInterface::class)
    ;

    $services->set(OperationUrlGeneratorInterface::class, OperationUrlGenerator::class)
        ->args([
            '$router' => service('router'),
            '$mapper' => service(ParametersMapperInterface::class),
        ])
        ->alias('lag_admin.operation.url_generator', OperationUrlGeneratorInterface::class)
    ;
    $services->set(ParametersMapperInterface::class, ParametersMapper::class);

    $services->set(LinkUrlGeneratorInterface::class, LinkUrlGenerator::class)
        ->args([
            '$operationUrlGenerator' => service(OperationUrlGeneratorInterface::class),
            '$urlGenerator' => service(UrlGeneratorInterface::class),
            '$operationFactory' => service('lag_admin.operation.factory'),
        ])
        ->alias('lag_admin.routing.link_url_generator', LinkUrlGeneratorInterface::class)
    ;

    $services->set(PathGeneratorInterface::class, PathGenerator::class);
    $services->set(UrlGeneratorInterface::class, UrlGenerator::class)
        ->args([
            '$router' => service('router'),
        ])
        ->alias('lag_admin.routing.url_generator', UrlGeneratorInterface::class)
    ;
};
