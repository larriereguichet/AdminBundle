<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Session\FlashMessageHelperInterface;
use LAG\AdminBundle\State\Processor\CompositeProcessor;
use LAG\AdminBundle\State\Processor\EventProcessor;
use LAG\AdminBundle\State\Processor\FlashMessageProcessor;
use LAG\AdminBundle\State\Processor\NormalizationProcessor;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Processor\ValidationProcessor;
use LAG\AdminBundle\State\Processor\WorkflowProcessor;
use LAG\AdminBundle\State\Provider\CompositeProvider;
use LAG\AdminBundle\State\Provider\NormalizationProvider;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\State\Provider\SerializationProvider;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Data providers
    $services->set(ProviderInterface::class, CompositeProvider::class)
        ->arg('$providers', tagged_iterator('lag_admin.state_provider'))
    ;
    $services->set(SerializationProvider::class)
        ->decorate(ProviderInterface::class, priority: -255)
        ->arg('$provider', service('.inner'))
        ->arg('$serializer', service('serializer'))
    ;
    $services->set(NormalizationProvider::class)
        ->decorate(ProviderInterface::class, priority: -200)
        ->arg('$provider', service('.inner'))
        ->arg('$normalizer', service('serializer'))
        ->arg('$denormalizer', service('serializer'))
    ;

    // Data processors
    $services->set(ProcessorInterface::class, CompositeProcessor::class)
        ->arg('$processors', tagged_iterator('lag_admin.state_processor'))
    ;
    $services->set(NormalizationProcessor::class)
        ->decorate(ProcessorInterface::class, priority: 255)
        ->arg('$processor', service('.inner'))
        ->arg('$normalizer', service('serializer'))
        ->arg('$denormalizer', service('serializer'))
    ;
    $services->set(EventProcessor::class)
        ->decorate(ProcessorInterface::class, priority: 200)
        ->arg('$processor', service('.inner'))
        ->arg('$eventDispatcher', service(ResourceEventDispatcherInterface::class))
    ;
    $services->set(ValidationProcessor::class)
        ->decorate(ProcessorInterface::class, priority: 100)
        ->arg('$processor', service('.inner'))
        ->arg('$validator', service('validator'))
    ;
    $services->set(WorkflowProcessor::class)
        ->decorate(ProcessorInterface::class, priority: 20)
        ->arg('$processor', service('.inner'))
        ->arg('$workflowRegistry', service('workflow.registry'))
    ;
    $services->set(FlashMessageProcessor::class)
        ->decorate(ProcessorInterface::class, priority: -200)
        ->arg('$processor', service('.inner'))
        ->arg('$flashMessageHelper', service(FlashMessageHelperInterface::class))
    ;

};
