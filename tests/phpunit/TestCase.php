<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Finder;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Assert that the given service class is configured in the services.yaml.
     */
    protected function assertServiceExists(string $serviceClass): void
    {
        $containerBuilder = $this->buildContainer();
        $exists = false;

        foreach ($containerBuilder->getDefinitions() as $id => $definition) {
            if ($serviceClass === $definition->getClass()) {
                $exists = true;
            }

            if ($serviceClass === $id) {
                $exists = true;
            }
        }
        if ($containerBuilder->hasAlias($serviceClass)) {
            $exists = true;
        }

        $this->assertTrue($exists, 'Failed asserting that the service "'.$serviceClass.'" exists');
    }

    protected function assertServiceHasTag(string $serviceId, string $tag): void
    {
        $containerBuilder = $this->buildContainer();
        $definition = $containerBuilder->getDefinition($serviceId);
        $this->assertTrue($definition->hasTag($tag));
    }

    protected function buildContainer(): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();
        $testResourcesDirectory = __DIR__.'/../../config';
        $locator = new FileLocator([
            $testResourcesDirectory,
        ]);
        $loader = new YamlFileLoader($containerBuilder, $locator);
        $finder = new Finder();
        $finder
            ->in($testResourcesDirectory.'/services')
            ->name('*.yaml')
            ->files()
        ;
        $loader->load('services.yaml');

        return $containerBuilder;
    }

    protected function assertSubscribedMethodsExists(EventSubscriberInterface $subscriber): void
    {
        $methods = forward_static_call([
            $subscriber::class,
            'getSubscribedEvents',
        ]);
        $this->assertIsArray($methods);

        foreach ($methods as $method) {
            if (\is_array($method)) {
                $this->assertArrayHasKey(0, $method);
                $method = $method[0];
            } else {
                $this->assertIsString($method);
            }
            $this->assertTrue(method_exists($subscriber, $method));
        }
    }

    protected function setPrivateProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new \ReflectionClass($object);

        $property = $reflection->getProperty($property);
        $property->setValue($object, $value);
    }

    protected function getPrivateProperty(object $object, string $property): mixed
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($property);

        return $property->getValue($object);
    }

    protected function createApplicationConfigurationMock(array $map): MockObject
    {
        $applicationConfiguration = $this->createMock(ApplicationConfiguration::class);
        $applicationConfiguration
            ->method('get')
            ->willReturnMap($map)
        ;

        return $applicationConfiguration;
    }

    protected function createContainerDefinition(string $class): Definition
    {
        return new Definition($class);
    }

    protected function createApplicationConfiguration(array $applicationConfiguration): ApplicationConfiguration
    {
        return new ApplicationConfiguration($applicationConfiguration);
    }
}
