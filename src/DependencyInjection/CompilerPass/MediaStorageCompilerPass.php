<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class MediaStorageCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $mediaStorageServiceId = $container->getParameter('lag_admin.media_storage');

        if ($container->hasDefinition('lag_admin.uploader')) {
            $uploaderDefinition = $container->getDefinition('lag_admin.uploader');
            $uploaderDefinition->setArgument('$filesystem', new Reference($mediaStorageServiceId));
        }
    }
}
