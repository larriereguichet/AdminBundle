<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;
use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractorInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(AdminDataCollector::class)
        ->arg('$registry', service(ResourceRegistryInterface::class))
        ->arg('$parametersExtractor', service(ResourceParametersExtractorInterface::class))
        ->tag('data_collector', [
            'template' => '@LAGAdmin/debug/template.html.twig',
            'id' => AdminDataCollector::class,
        ])
        ->private()
    ;
};
