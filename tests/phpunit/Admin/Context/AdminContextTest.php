<?php

namespace LAG\AdminBundle\Tests\Admin\Context;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Context\AdminContext;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\TestCase;

class AdminContextTest extends TestCase
{
    public function testWithoutAdmin(): void
    {
        $helper = new AdminContext();

        $this->assertFalse($helper->hasAdmin());
        $this->expectException(Exception::class);
        $helper->getAdmin();
    }

    public function testWithAdmin(): void
    {
        $helper = new AdminContext();
        $this->assertFalse($helper->hasAdmin());

        $admin = $this->createMock(AdminInterface::class);
        $helper->setAdmin($admin);
        $this->assertTrue($helper->hasAdmin());
        $this->assertEquals($admin, $helper->getAdmin());

        $this->expectException(Exception::class);
        $helper->setAdmin($admin);
    }
}
