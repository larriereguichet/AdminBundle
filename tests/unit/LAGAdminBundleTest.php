<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests;

use LAG\AdminBundle\DependencyInjection\CompilerPass\WorkflowCompilerPass;
use LAG\AdminBundle\LAGAdminBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class LAGAdminBundleTest extends TestCase
{
    private LAGAdminBundle $bundle;

    public function testBuild(): void
    {
        $container = new ContainerBuilder();
        $this->bundle->build($container);
        $contains = false;

        foreach ($container->getCompilerPassConfig()->getPasses() as $pass) {
            if ($pass instanceof WorkflowCompilerPass) {
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
