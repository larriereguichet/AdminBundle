<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

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

    $services->set(ImagePathGeneratorInterface::class, ImagePathGenerator::class);
};
