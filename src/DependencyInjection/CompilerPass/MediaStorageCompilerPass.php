<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use LAG\AdminBundle\Upload\Uploader\ImageUploaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class MediaStorageCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $mediaStorageServiceId = $container->getParameter('lag_admin.media_storage');
        $serviceId = $this->resolveServiceId($container);

        if ($serviceId !== null) {
            $container
                ->getDefinition($serviceId)
                ->setArgument('$filesystem', new Reference($mediaStorageServiceId));
        }
    }

    private function resolveServiceId(ContainerBuilder $container): ?string
    {
        if ($container->hasDefinition('lag_admin.uploader')) {
            return 'lag_admin.uploader';
        }

        if ($container->hasAlias('lag_admin.uploader')) {
            return (string) $container->getAlias('lag_admin.uploader');
        }

        if ($container->hasDefinition(ImageUploaderInterface::class)) {
            return ImageUploaderInterface::class;
        }

        return null;
    }
}
