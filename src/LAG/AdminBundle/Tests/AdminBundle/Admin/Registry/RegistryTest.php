<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Registry;

use Exception;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Registry\Registry;
use LAG\AdminBundle\Tests\AdminTestBase;

class RegistryTest extends AdminTestBase
{
    public function testRegistry()
    {
        $registry = new Registry();
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $admin
            ->method('getName')
            ->willReturn('myAdmin')
        ;

        // add SHOULD work properly
        $registry->add($admin);

        // an exception SHOULD be thrown if an Admin with the same has already been registered
        $this->assertExceptionRaised(Exception::class, function () use ($registry, $admin) {
            $registry->add($admin);
        });
    
        $this->assertEquals($admin, $registry->get('myAdmin'));
        $this->assertEquals([
            'myAdmin' => $admin
        ], $registry->all());
        $this->assertTrue($registry->has('myAdmin'));
    
        $this->assertExceptionRaised(Exception::class, function() use ($registry) {
            $registry->get('wrong');
        });
    }
}
