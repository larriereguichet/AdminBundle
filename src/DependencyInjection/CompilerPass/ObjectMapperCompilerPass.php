<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use LAG\AdminBundle\Mapper\ObjectMapperInterface;
use LAG\AdminBundle\State\Processor\MappingProcessor;
use LAG\AdminBundle\State\Provider\MappingProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\ObjectMapper\ObjectMapperInterface as SymfonyObjectMapperInterface;

/**
 * The symfony/object-mapper component being installed does not mean the FrameworkBundle registered its
 * services: it only does so when the component is a non-development dependency. Remove the input and
 * output mapping services when no object mapper is available, instead of letting the container fail.
 */
final readonly class ObjectMapperCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has(SymfonyObjectMapperInterface::class)) {
            return;
        }
        $container->removeDefinition(ObjectMapperInterface::class);
        $container->removeDefinition(MappingProvider::class);
        $container->removeDefinition(MappingProcessor::class);
    }
}
