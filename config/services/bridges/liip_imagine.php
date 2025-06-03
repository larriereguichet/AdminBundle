<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\LiipImagine\DataTransformer\ImageDataTransformer;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->set(ImageDataTransformer::class)
        ->tag('lag_admin.data_transformer')
        ->arg('$filterExtension', service('liip_imagine.templating.filter_runtime'))
    ;
};
