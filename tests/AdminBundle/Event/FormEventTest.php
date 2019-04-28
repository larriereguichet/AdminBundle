<?php

namespace LAG\AdminBundle\Tests\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Field\Definition\FieldDefinition;
use LAG\AdminBundle\Tests\AdminTestBase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormEventTest extends AdminTestBase
{
    public function testEvent()
    {
        $admin = $this->createMock(AdminInterface::class);
        $form = $this->createMock(FormInterface::class);
        $definition = new FieldDefinition('panda_type');
        $request = new Request();

        $event = new FormEvent($admin, $request);

        $event->addForm($form, 'my_panda_form');

        $this->assertCount(1, $event->getForms());
        $this->assertArrayHasKey('my_panda_form', $event->getForms());
        $this->assertEquals($form, $event->getForms()['my_panda_form']);
        $this->assertExceptionRaised(Exception::class, function () use ($event, $form) {
            $event->addForm($form, 'my_panda_form');
        });

        $event->addFieldDefinition('panda_field', $definition);

        $this->assertCount(1, $event->getFieldDefinitions());
        $this->assertArrayHasKey('panda_field', $event->getFieldDefinitions());
        $this->assertEquals($definition, $event->getFieldDefinitions()['panda_field']);
    }
}
