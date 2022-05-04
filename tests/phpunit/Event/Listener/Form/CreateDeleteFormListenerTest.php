<?php

namespace LAG\AdminBundle\Tests\Event\Listener\Form;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Event\Listener\Form\CreateDeleteFormListener;
use LAG\AdminBundle\Form\Factory\FormFactoryInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CreateDeleteFormListenerTest extends TestCase
{
    private CreateDeleteFormListener $listener;
    private MockObject $formFactory;

    public function testService(): void
    {
        $this->assertServiceExists(CreateDeleteFormListener::class);
    }

    public function testInvoke(): void
    {
        $request = new Request();
        $admin = $this->createMock(AdminInterface::class);
        $action = $this->createMock(ActionInterface::class);
        $deleteForm = $this->createMock(FormInterface::class);
        $data = new \stdClass();

        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data)
        ;

        $action
            ->expects($this->once())
            ->method('getName')
            ->willReturn('delete')
        ;
        $this
            ->formFactory
            ->expects($this->once())
            ->method('createDeleteForm')
            ->with($admin, $request, $data)
            ->willReturn($deleteForm)
        ;
        $formEvent = new FormEvent($admin, $request);

        $this->listener->__invoke($formEvent);

        $this->assertTrue($formEvent->hasForm('delete'));
        $this->assertEquals($deleteForm, $formEvent->getForms()['delete']);
    }

    protected function setUp(): void
    {
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->listener = new CreateDeleteFormListener($this->formFactory);
    }
}
