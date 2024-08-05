<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests;

use LAG\AdminBundle\LAGAdminBundle;
use Nyholm\BundleTest\TestKernel;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class KernelTestBase extends TestCase
{
    protected function getBundleClass(): string
    {
        return LAGAdminBundle::class;
    }

    protected function createKernel(): KernelInterface
    {
        $kernel = new TestKernel('test', true);
        $kernel->setTestProjectDir(__DIR__.'/../app');

        $bundles = include __DIR__.'/../app/config/bundles.php';

        foreach (array_keys($bundles) as $bundle) {
            $kernel->addTestBundle($bundle);
        }

        $kernel->boot();

        return $kernel;
    }
}
