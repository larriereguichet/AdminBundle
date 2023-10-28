<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Controller\Resource\ResourceCollectionController;
use LAG\AdminBundle\Controller\Resource\ResourceController;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Response\Handler\RedirectHandlerInterface;
use LAG\AdminBundle\State\Processor\DataProcessorInterface;
use LAG\AdminBundle\State\Provider\DataProviderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(ResourceController::class)
        ->arg('$uriVariablesExtractor', service(UriVariablesExtractorInterface::class))
        ->arg('$contextProvider', service(ContextProviderInterface::class))
        ->arg('$dataProvider', service(DataProviderInterface::class))
        ->arg('$dataProcessor', service(DataProcessorInterface::class))
        ->arg('$formFactory', service('form.factory'))
        ->arg('$redirectionHandler', service(RedirectHandlerInterface::class))
        ->tag('controller.service_arguments')
    ;

    $services->set(ResourceCollectionController::class)
        ->arg('$uriVariablesExtractor', service(UriVariablesExtractorInterface::class))
        ->arg('$contextProvider', service(ContextProviderInterface::class))
        ->arg('$dataProvider', service(DataProviderInterface::class))
        ->arg('$formFactory', service('form.factory'))
        ->arg('$environment', service('twig'))
        ->tag('controller.service_arguments')
    ;
};
