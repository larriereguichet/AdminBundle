<?php

namespace LAG\AdminBundle\Tests;

use Closure;
use Exception;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Assert that the given service class is configured in the services.yaml.
     */
    protected function assertServiceExists(string $serviceClass): void
    {
        $containerBuilder = new ContainerBuilder();
        $testResourcesDirectory = __DIR__.'/../../src/Resources/config';
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

    /**
     * Assert that methods declared in the getSubscribedEvents() really exists.
     */
    protected function assertSubscribedMethodsExists(EventSubscriberInterface $subscriber)
    {
        $methods = forward_static_call([
            get_class($subscriber),
            'getSubscribedEvents',
        ]);
        $this->assertIsArray($methods);

        foreach ($methods as $method) {
            if (is_array($method)) {
                $this->assertArrayHasKey(0, $method);
                $method = $method[0];
            } else {
                $this->assertIsString($method);
            }
            $this->assertTrue(method_exists($subscriber, $method));
        }
    }

    /**
     * Assert that an exception is raised in the given code.
     *
     * @deprecated Use expectException() from PHPUnit
     *
     * @param string $exceptionClass
     */
    protected function assertExceptionRaised($exceptionClass, Closure $closure)
    {
        $e = null;
        $isClassValid = false;
        $message = '';

        try {
            $closure();
        } catch (Exception $e) {
            if (get_class($e) == $exceptionClass) {
                $isClassValid = true;
            }
            $message = $e->getMessage();
        }
        $this->assertNotNull($e, 'No Exception was thrown');
        $this->assertTrue($isClassValid, sprintf('Expected %s, got %s (Exception message : "%s")',
            $exceptionClass,
            get_class($e),
            $message
        ));
    }

    protected function setPrivateProperty($object, $property, $value)
    {
        $reflection = new ReflectionClass($object);

        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    protected function getPrivateProperty($object, $property)
    {
        $reflection = new ReflectionClass($object);

        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    protected function createActionConfigurationMock(array $map)
    {
        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnMap($map)
        ;

        return $actionConfiguration;
    }

    /**
     * @return MockObject|AdminConfiguration
     */
    protected function createAdminConfigurationMock(array $map): MockObject
    {
        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnMap($map)
        ;

        return $adminConfiguration;
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

    /**
     * @param int   $expectedCalls
     * @param int   $configurationExpectedCalls
     *
     * @return MockObject|ActionInterface
     */
    protected function createActionWithConfigurationMock(
        array $map
    ): MockObject {
        $configuration = $this->createActionConfigurationMock($map);

        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;

        return $action;
    }

    /**
     * @param int   $expectedCalls
     * @param int   $configurationExpectedCalls
     *
     * @return MockObject|AdminInterface
     */
    protected function createAdminWithConfigurationMock(
        array $map = [],
        Request $request = null
    ): MockObject {
        $configuration = $this->createAdminConfigurationMock($map);

        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;

        if (null !== $request) {
            $admin
                ->expects($this->atLeastOnce())
                ->method('getRequest')
                ->willReturn($request)
            ;
        }

        return $admin;
    }

    protected function createContainerDefinition(string $class): Definition
    {
        return new Definition($class);
    }

    protected function createApplicationConfiguration(array $appConfig): ApplicationConfiguration
    {
        return new ApplicationConfiguration($appConfig);
    }
}
