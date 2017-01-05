<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Action\Registry;

use Exception;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Registry\Registry;
use LAG\AdminBundle\Tests\AdminTestBase;

class RegistryTest extends AdminTestBase
{
    public function testAdd()
    {
        $registry = new Registry();
        $action = $this
            ->getMockBuilder(ActionInterface::class)
            ->getMock();
        $action
            ->method('getName')
            ->willReturn('myAction');

        // add SHOULD work properly
        $registry->add('my.action', $action);

        // an exception SHOULD be thrown if an Action with the same has already been registered
        $this->assertExceptionRaised(Exception::class, function () use ($registry, $action) {
            $registry->add('my.action', $action);
        });
    
        $this->assertExceptionRaised(Exception::class, function () use ($registry, $action) {
            $registry->get('badName');
        });
    
        $this->assertTrue($registry->has('my.action'));
        $this->assertEquals($action, $registry->get('my.action'));
        $this->assertEquals([
            'my.action' => $action
        ], $registry->all());
    }
}
