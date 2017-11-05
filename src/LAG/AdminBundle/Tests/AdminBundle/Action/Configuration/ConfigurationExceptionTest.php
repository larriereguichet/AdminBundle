<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Configuration;

use Exception;
use LAG\AdminBundle\Action\Configuration\ConfigurationException;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Tests\AdminTestBase;

class ConfigurationExceptionTest extends AdminTestBase
{
    /**
     * The admin and action should be added at the end of the message.
     */
    public function testException()
    {
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getName')
            ->willReturn('MyAdmin')
        ;
        
        $exception = new ConfigurationException(
            'My little message',
            'an_action',
            $admin->getName()
        );

        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertEquals('My little message, for Admin MyAdmin and action an_action', $exception->getMessage());
    
        $exception = new ConfigurationException(
            'My little message',
            'an_action'
        );
        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertEquals('My little message, for Admin unknown and action an_action', $exception->getMessage());
    }
}
