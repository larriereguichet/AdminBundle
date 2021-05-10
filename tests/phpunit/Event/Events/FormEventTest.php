<?php

namespace LAG\AdminBundle\Tests\Event\Events;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Exception\Form\FormMissingException;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormEventTest extends TestCase
{
    public function testEvent(): void
    {
        $admin = $this->createMock(AdminInterface::class);
        $request = new Request();

        $event = new FormEvent($admin, $request);

        $form = $this->createMock(FormInterface::class);
        $this->assertFalse($event->hasForm('entity'));
        $this->assertEquals([], $event->getForms());
        $event->addForm('entity', $form);
        $this->assertEquals(['entity' => $form], $event->getForms());
        $this->assertTrue($event->hasForm('entity'));
        $event->removeForm('entity');
        $this->assertFalse($event->hasForm('entity'));

        $this->assertExceptionRaised(FormMissingException::class, function () use ($event) {
            $event->removeForm('missing_form');
        });
    }
}
