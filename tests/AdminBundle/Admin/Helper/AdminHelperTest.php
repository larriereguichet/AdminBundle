<?php

namespace LAG\AdminBundle\Tests\Admin\Helper;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelper;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\TestCase;

class AdminHelperTest extends TestCase
{
    public function testSettersAndGetters()
    {
        $helper = new AdminHelper();

        $this->assertExceptionRaised(Exception::class, function () use ($helper) {
            $helper->getAdmin();
        });

        $admin = $this->createMock(AdminInterface::class);
        $helper->setAdmin($admin);

        $this->assertEquals($admin, $helper->getAdmin());

        $this->assertExceptionRaised(Exception::class, function () use ($helper, $admin) {
            $helper->setAdmin($admin);
        });
    }
}
