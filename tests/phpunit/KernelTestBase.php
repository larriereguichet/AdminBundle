<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests;

use LAG\AdminBundle\LAGAdminBundle;
use legacy\Fixtures\Kernel\TestKernel;

abstract class KernelTestBase extends TestCase
{
    protected function getBundleClass(): string
    {
        return LAGAdminBundle::class;
    }

    protected function createKernel(): TestKernel
    {
        $kernel = new TestKernel('test', true);
        $kernel->setProjectDir(__DIR__.'/../app');
        $kernel->setCacheDir(__DIR__.'/../app/var/cache');

        $bundles = include __DIR__.'/../app/config/bundles.php';

        foreach (array_keys($bundles) as $bundle) {
            $kernel->addBundle($bundle);
        }

        $kernel->boot();

        return $kernel;
    }
}
