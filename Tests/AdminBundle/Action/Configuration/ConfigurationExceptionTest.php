<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Configuration;

use Exception;
use LAG\AdminBundle\Action\Configuration\ConfigurationException;
use LAG\AdminBundle\Tests\AdminTestBase;

class ConfigurationExceptionTest extends AdminTestBase
{
    /**
     * The admin and action should be added at the end of the message.
     */
    public function testException()
    {
        $exception = new ConfigurationException(
            'My little message',
            'an_action',
            $this->createAdmin('test', [
                'entity' => 'whatever',
                'form' => 'whatever'
            ])
        );

        $this->assertInstanceOf(Exception::class, $exception);
        $this->assertEquals('My little message, for Admin test and action an_action', $exception->getMessage());
    }
}
