<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

/*
 * Symfony UX Twig Component 3 makes both of these mandatory, where 2 defaulted them. The bundle
 * registers its own components with an explicit key and template, so it needs neither: they are
 * declared here because the test application boots the extension, and an application consuming the
 * bundle declares them the same way, usually through the Flex recipe.
 */
return static function (ContainerConfigurator $container): void {
    $container->extension('twig_component', [
        'defaults' => [
            'LAG\AdminBundle\Tests\Application\Twig\Component\\' => 'components/',
        ],
        'anonymous_template_directory' => 'components',
    ]);
};
