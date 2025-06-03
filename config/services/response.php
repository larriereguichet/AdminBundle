<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Response\Handler\ContentResponseHandlerInterface;
use LAG\AdminBundle\Response\Handler\ContextResponseHandler;
use LAG\AdminBundle\Response\Handler\FormResponseHandler;
use LAG\AdminBundle\Response\Handler\JsonResponseHandler;
use LAG\AdminBundle\Response\Handler\RedirectResponseHandler;
use LAG\AdminBundle\Response\Handler\RedirectResponseHandlerInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandler;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\Response\Handler\TemplateResponseHandler;
use LAG\AdminBundle\Routing\UrlGenerator\ResourceUrlGeneratorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Response handlers
    $services->set(ResponseHandlerInterface::class, ResponseHandler::class)
        ->alias('lag_admin.response_handler', ResponseHandlerInterface::class)
    ;
    $services->set(ContentResponseHandlerInterface::class, TemplateResponseHandler::class)
        ->args(['$environment' => service('twig')])
    ;
    $services->set(FormResponseHandler::class)
        ->decorate(id: ResponseHandlerInterface::class, priority: 250)
        ->args(['$responseHandler' => service('.inner')])
    ;
    $services->set(JsonResponseHandler::class)
        ->decorate(id: ResponseHandlerInterface::class, priority: 250)
        ->args([
            '$requestStack' => service('request_stack'),
            '$responseHandler' => service('.inner'),
            '$serializer' => service('serializer'),
        ])
    ;

    // Redirect handler
    $services->set(RedirectResponseHandlerInterface::class, RedirectResponseHandler::class)
        ->args(['$urlGenerator' => service(ResourceUrlGeneratorInterface::class)])
        ->alias('lag_admin.redirect_handler', RedirectResponseHandlerInterface::class)
    ;
};
