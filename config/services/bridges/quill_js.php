<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\QuillJs\Render\QuillJsRenderer;
use LAG\AdminBundle\RichText\RichTextRendererInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(RichTextRendererInterface::class, QuillJsRenderer::class);
};
