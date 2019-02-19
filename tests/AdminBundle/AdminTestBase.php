<?php

namespace LAG\AdminBundle\Tests;

use Closure;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdminTestBase extends TestCase
{
    /**
     * Assert that the given service class is configured in the services.yaml
     *
     * @param string $serviceClass
     */
    protected function assertServiceExists(string $serviceClass)
    {
        $containerBuilder = new ContainerBuilder();
        $locator = new FileLocator([
            __DIR__.'/../../src/Resources/config',
        ]);
        $loader = new YamlFileLoader($containerBuilder, $locator);
        $loader->load('services.yaml');
        $exists = false;

        foreach ($containerBuilder->getDefinitions() as $definition) {
            if ($serviceClass === $definition->getClass()) {
                $exists = true;
            }
        }
        $this->assertTrue($exists);
    }

    /**
     * Assert that methods declared in the getSubscribedEvents() really exists.
     *
     * @param EventSubscriberInterface $subscriber
     */
    protected function assertSubscribedMethodsExists(EventSubscriberInterface $subscriber)
    {
        $methods = forward_static_call([
            get_class($subscriber),
            'getSubscribedEvents'
        ]);
        $this->assertInternalType('array', $methods);

        foreach ($methods as $method) {
            $this->assertTrue(method_exists($subscriber, $method));
        }
    }

    /**
     * Assert that an exception is raised in the given code.
     *
     * @param $exceptionClass
     * @param Closure $closure
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
    
    /**
     * @param $class
     *
     * @return MockObject|mixed
     */
    protected function createMock($class)
    {
        return parent::createMock($class);
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
}
