<?php

namespace LAG\AdminBundle\Tests\Admin\Helper;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Helper\AdminHelper;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Tests\AdminTestBase;

class AdminHelperTest extends AdminTestBase
{
    public function testSettersAndGetters()
    {
        $helper = new AdminHelper();

        $this->assertEquals(null, $helper->getCurrent());

        $admin = $this->createMock(AdminInterface::class);
        $helper->setCurrent($admin);

        $this->assertEquals($admin, $helper->getCurrent());

        $this->assertExceptionRaised(Exception::class, function () use ($helper, $admin) {
            $helper->setCurrent($admin);
        });
    }
}
