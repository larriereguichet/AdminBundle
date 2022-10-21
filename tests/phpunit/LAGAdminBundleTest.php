<?php

namespace LAG\AdminBundle\Tests;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class LAGAdminBundleTest extends KernelTestBase
{
    public function testInitBundle()
    {
        $kernel = $this->createKernel();

        // Test if the container can boot
        $kernel->getContainer();
        $this->assertTrue(true);
    }
}
