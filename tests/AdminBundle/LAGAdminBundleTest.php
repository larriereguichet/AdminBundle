<?php

namespace LAG\AdminBundle\Tests;

use LAG\AdminBundle\DependencyInjection\CompilerPass\ResourceCompilerPass;
use LAG\AdminBundle\LAGAdminBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LAGAdminBundleTest extends AdminTestBase
{
    public function testBuild()
    {
        $container = $this->getMockWithoutConstructor(ContainerBuilder::class);
        $container
            ->expects($this->exactly(2))
            ->method('addCompilerPass')
            ->willReturnMap([
                [new ResourceCompilerPass()],
            ])
        ;

        $bundle = new LAGAdminBundle();
        $bundle->build($container);
    }
}
