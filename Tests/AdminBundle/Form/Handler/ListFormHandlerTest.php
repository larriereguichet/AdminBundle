<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Form\Handler;

use LAG\AdminBundle\Form\Handler\ListFormHandler;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Entity\TestEntity;
use Symfony\Component\Form\Form;

class ListFormHandlerTest extends AdminTestBase
{
    public function testHandle()
    {
        $form = $this
            ->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->getMock();
        $form
            ->method('getData')
            ->willReturn([
                'batch_action' => [],
                'entities' => [
                    new TestEntity(23, 'test'),
                    new TestEntity(42, 'test'),
                    new TestEntity(64, 'test'),
                ],
                'batch_23' => true,
                'batch_42' => true,
            ]);

        $handler = new ListFormHandler();
        $data = $handler->handle($form);

        $this->assertContains(23, $data['ids']);
        $this->assertContains(42, $data['ids']);
    }
}
