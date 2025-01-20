<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Filter\Applicator\CompositeFilterApplicator;
use LAG\AdminBundle\Filter\Applicator\FilterApplicatorInterface;
use LAG\AdminBundle\Filter\Factory\EventFilterFactory;
use LAG\AdminBundle\Filter\Factory\FilterFactory;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Filter\Resolver\FilterValuesResolverInterface;
use LAG\AdminBundle\State\Provider\FilterProvider;
use LAG\AdminBundle\State\Provider\ProviderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Factories
    $services->set(FilterFactoryInterface::class, FilterFactory::class)
        ->arg('$validator', service('validator'))
    ;
    $services->set(EventFilterFactory::class)
        ->decorate(FilterFactoryInterface::class)
        ->arg('$eventDispatcher', service(ResourceEventDispatcherInterface::class))
        ->arg('$decorated', service('.inner'))
    ;

    // Applicators
    $services->set(FilterApplicatorInterface::class, CompositeFilterApplicator::class)
        ->arg('$applicators', tagged_iterator(FilterApplicatorInterface::SERVICE_TAG))
    ;
};
