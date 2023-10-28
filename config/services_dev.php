<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(AdminDataCollector::class)
        ->arg('$registry', service(ResourceRegistryInterface::class))
        ->arg('$applicationConfiguration', service(ApplicationConfiguration::class))
        ->arg('$parametersExtractor', service(ParametersExtractorInterface::class))
        ->tag('data_collector', [
            'template' => '@LAGAdmin/debug/template.html.twig',
            'id' => AdminDataCollector::class,
        ])
        ->private()
    ;
};
