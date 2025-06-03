<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Filter\Applicator\CompositeFilterApplicator;
use LAG\AdminBundle\Filter\Applicator\FilterApplicatorInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Applicators
    $services->set(FilterApplicatorInterface::class, CompositeFilterApplicator::class)
        ->arg('$applicators', tagged_iterator(FilterApplicatorInterface::SERVICE_TAG))
    ;
};
