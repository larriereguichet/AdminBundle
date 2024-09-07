<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests;

use LAG\AdminBundle\Tests\DependencyInjection\CompilerPass\PublicServiceCompilerPass;
use Nyholm\BundleTest\TestKernel;
use Symfony\Component\HttpKernel\KernelInterface;

trait ContainerTestTrait
{
    private static ?KernelInterface $kernel = null;

    protected static function bootKernel(): KernelInterface
    {
        if (self::$kernel !== null) {
            return self::$kernel;
        }
        $testDirectory = __DIR__.'/../app';
        $bundles = include $testDirectory.'/config/bundles.php';

        $kernel = new TestKernel('test', true);
        $kernel->setTestProjectDir($testDirectory);

        foreach (array_keys($bundles) as $bundle) {
            $kernel->addTestBundle($bundle);
        }
        $kernel->addTestConfig($testDirectory.'/config/config.php');
        $kernel->addTestRoutingFile($testDirectory.'/config/routing.php');
        $kernel->addTestCompilerPass(new PublicServiceCompilerPass());
        $kernel->boot();

        self::$kernel = $kernel;

        return $kernel;
    }

    /** Assert that the given service class is configured in the service configuration */
    protected static function assertService(string $serviceId): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        self::assertTrue($container->has($serviceId), \sprintf('The service container does not has a service "%s".', $serviceId));

        $service = $container->get($serviceId);

        self::assertNotNull($service);
    }

    protected static function assertNoService(string $serviceId): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        self::assertFalse($container->has($serviceId), \sprintf('The service container has a service "%s".', $serviceId));
    }
}
