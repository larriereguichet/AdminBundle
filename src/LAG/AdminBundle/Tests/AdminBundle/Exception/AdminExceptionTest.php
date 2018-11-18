<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Exception;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Exception\AdminException;
use LAG\AdminBundle\Tests\AdminTestBase;

class AdminExceptionTest extends AdminTestBase
{
    public function testException()
    {
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getName')
            ->willReturn('My Admin')
        ;
        
        $exception = new AdminException('my_message', 'my_action', $admin);
    
        $this->assertEquals('my_message, for Admin My Admin and action my_action', $exception->getMessage());
    }
}
