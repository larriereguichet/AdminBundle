<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests;

use LAG\AdminBundle\DependencyInjection\CompilerPass\EventCompilerPass;
use LAG\AdminBundle\LAGAdminBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LAGAdminBundleTest extends TestCase
{
    private LAGAdminBundle $bundle;

    public function testBuild(): void
    {
        $container = new ContainerBuilder();
        $this->bundle->build($container);
        $contains = false;

        foreach ($container->getCompilerPassConfig()->getPasses() as $pass) {
            if ($pass instanceof EventCompilerPass) {
                $contains = true;
            }
        }
        $this->assertTrue($contains);

        $this->assertEquals(realpath(__DIR__.'/../..'), $this->bundle->getPath());
    }

    protected function setUp(): void
    {
        $this->bundle = new LAGAdminBundle();
    }
}
