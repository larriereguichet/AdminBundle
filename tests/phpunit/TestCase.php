<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Assert that the given service class is configured in the service configuration
     */
    protected function assertServiceExists(string $serviceId): void
    {
        $container = $this->buildContainer();
        $container->compile();

        self::assertTrue($container->has($serviceId));
    }

    protected function assertServiceHasTag(string $serviceId, string $tag): void
    {
        $containerBuilder = $this->buildContainer();
        $definition = $containerBuilder->getDefinition($serviceId);
        $this->assertTrue($definition->hasTag($tag));
    }

    protected function buildContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $testResourcesDirectory = __DIR__.'/../../config';
        $locator = new FileLocator([$testResourcesDirectory]);
        $loader = new Loader\PhpFileLoader($container, $locator);
        $loader->load('services.php');

        return $container;
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

    protected function createContainerDefinition(string $class): Definition
    {
        return new Definition($class);
    }
}
