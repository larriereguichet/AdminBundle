<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use LAG\AdminBundle\Filter\Factory\EventFilterFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EventCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->getParameter('lag_admin.filter_events') === false) {
            $container->removeDefinition(EventFilterFactory::class);
        }
    }
}
