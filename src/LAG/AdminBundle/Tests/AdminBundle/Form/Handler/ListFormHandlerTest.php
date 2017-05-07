<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Form\Handler;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Form\Handler\ListFormHandler;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Entity\TestSimpleEntity;
use Symfony\Component\Form\Form;

class ListFormHandlerTest extends AdminTestBase
{
    public function testTmp()
    {
        // TODO remove
        $this->assertTrue(true);
    }
    
//    public function testHandle()
//    {
//
//        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
//
//        $form = $this
//            ->getMockBuilder(Form::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//        $form
//            ->method('getData')
//            ->willReturn([
//                'batch_action' => [],
//                'entities' => [
//                    new TestSimpleEntity(23, 'test'),
//                    new TestSimpleEntity(42, 'test'),
//                    new TestSimpleEntity(64, 'test'),
//                ],
//                'batch_23' => true,
//                'batch_42' => true,
//            ]);
//
//        $handler = new ListFormHandler();
//        $handler->handle($form, $admin);
//    }
}
