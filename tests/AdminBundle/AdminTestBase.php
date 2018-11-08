<?php

namespace LAG\AdminBundle\Tests;

use Closure;
use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class AdminTestBase extends TestCase
{
    /**
     * Assert that the given service class is configured in the services.yaml
     *
     * @param string $serviceClass
     */
    public function assertServiceExists(string $serviceClass)
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
     * @return PHPUnit_Framework_MockObject_MockObject|mixed
     */
    protected function getMockWithoutConstructor($class)
    {
        return $this
            ->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
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
