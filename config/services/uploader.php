<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\Flysystem\UrlGenerator\PublicUrlGenerator;
use LAG\AdminBundle\Upload\Generator\ImagePathGenerator;
use LAG\AdminBundle\Upload\Generator\ImagePathGeneratorInterface;
use LAG\AdminBundle\Upload\Uploader\ImageUploader;
use LAG\AdminBundle\Upload\Uploader\ImageUploaderInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // Uploader
    $services->set(ImageUploaderInterface::class, ImageUploader::class)
        ->arg('$pathGenerator', service(ImagePathGeneratorInterface::class))
        ->alias('lag_admin.uploader', ImageUploaderInterface::class)
    ;

    // Url generators
    // TODO remove
    $services->set(PublicUrlGenerator::class)
        ->arg('$mediaDirectory', param('lag_admin.media_directory'))
    ;
    $services->alias('lag_admin.filesystem.public_url_generator', PublicUrlGenerator::class);

    $services->set(ImagePathGeneratorInterface::class, ImagePathGenerator::class);
};
