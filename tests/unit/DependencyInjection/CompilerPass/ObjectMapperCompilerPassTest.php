<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\DependencyInjection\CompilerPass;

use LAG\AdminBundle\Bridge\ObjectMapper\SymfonyObjectMapper;
use LAG\AdminBundle\DependencyInjection\CompilerPass\ObjectMapperCompilerPass;
use LAG\AdminBundle\Mapper\ObjectMapperInterface;
use LAG\AdminBundle\State\Processor\MappingProcessor;
use LAG\AdminBundle\State\Provider\MappingProvider;
use LAG\AdminBundle\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\ObjectMapper\ObjectMapper;
use Symfony\Component\ObjectMapper\ObjectMapperInterface as SymfonyObjectMapperInterface;

final class ObjectMapperCompilerPassTest extends TestCase
{
    #[Test]
    public function itRemovesTheMappingServicesWhenNoObjectMapperIsRegistered(): void
    {
        $container = $this->createContainer();

        (new ObjectMapperCompilerPass())->process($container);

        self::assertFalse($container->hasDefinition(ObjectMapperInterface::class));
        self::assertFalse($container->hasDefinition(MappingProvider::class));
        self::assertFalse($container->hasDefinition(MappingProcessor::class));
    }

    #[Test]
    public function itKeepsTheMappingServicesWhenAnObjectMapperIsRegistered(): void
    {
        $container = $this->createContainer();
        $container->setDefinition(SymfonyObjectMapperInterface::class, new Definition(ObjectMapper::class));

        (new ObjectMapperCompilerPass())->process($container);

        self::assertTrue($container->hasDefinition(ObjectMapperInterface::class));
        self::assertTrue($container->hasDefinition(MappingProvider::class));
        self::assertTrue($container->hasDefinition(MappingProcessor::class));
    }

    private function createContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setDefinition(ObjectMapperInterface::class, new Definition(SymfonyObjectMapper::class));
        $container->setDefinition(MappingProvider::class, new Definition(MappingProvider::class));
        $container->setDefinition(MappingProcessor::class, new Definition(MappingProcessor::class));

        return $container;
    }
}
