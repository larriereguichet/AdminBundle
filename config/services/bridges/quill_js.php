<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use LAG\AdminBundle\Bridge\QuillJs\Render\QuillJsRenderer;
use LAG\AdminBundle\Bridge\QuillJs\Render\QuillJsRendererInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(QuillJsRendererInterface::class, QuillJsRenderer::class);
};
