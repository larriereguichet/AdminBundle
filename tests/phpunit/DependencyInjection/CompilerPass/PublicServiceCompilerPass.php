<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function Symfony\Component\String\u;

final readonly class PublicServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if (!u($id)->startsWith('lag_')  && !u($id)->startsWith('LAG\\')) {
                continue;
            }
            $definition->setPublic(true);
        }
    }
}
