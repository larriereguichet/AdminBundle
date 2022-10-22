<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests;

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
