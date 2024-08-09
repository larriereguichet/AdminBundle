<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\Flysystem\Registry\StorageRegistryInterface;
use LAG\AdminBundle\Bridge\Flysystem\UrlGenerator\PublicUrlGenerator;
use LAG\AdminBundle\Upload\Generator\ImagePathGenerator;
use LAG\AdminBundle\Upload\Generator\ImagePathGeneratorInterface;
use LAG\AdminBundle\Upload\Uploader\Uploader;
use LAG\AdminBundle\Upload\Uploader\UploaderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Uploader
    $services->set(UploaderInterface::class, Uploader::class)
        ->arg('$pathGenerator', service(ImagePathGeneratorInterface::class))
        ->arg('$registry', service(StorageRegistryInterface::class))
    ;

    // Url generators
    $services->set(PublicUrlGenerator::class)
        ->arg('$mediaDirectory', param('lag_admin.media_directory'))
    ;
    $services->alias('lag_admin.filesystem.public_url_generator', PublicUrlGenerator::class);

    $services->set(ImagePathGeneratorInterface::class, ImagePathGenerator::class);
};
