<?php

namespace LAG\AdminBundle\Tests\Event\Listener\Form;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Event\Listener\Form\AddDeleteFormListener;
use LAG\AdminBundle\Factory\AdminFormFactoryInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\HttpFoundation\Request;

class FormListenerTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testInvoke(string $actionName): void
    {
        [$listener, $formFactory] = $this->createListener();

        $request = new Request();
        $admin = $this->createMock(AdminInterface::class);
        $action = $this->createMock(ActionInterface::class);

        $data = new stdClass();

        $event = $this->createMock(FormEvent::class);
        $event
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;
        $event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($request)
        ;
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;

        $action
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($actionName)
        ;

        $admin
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data)
        ;

        if (in_array($actionName, ['create', 'edit'])) {
            $formFactory
                ->expects($this->once())
                ->method('createEntityForm')
                ->with($admin, $request, $data)
            ;
        }

        if (in_array($actionName, ['delete'])) {
            $formFactory
                ->expects($this->once())
                ->method('createDeleteForm')
                ->with($admin, $request, $data)
            ;
        }

        $listener->__invoke($event);
    }

    public function dataProvider(): array
    {
        return [
            ['create'],
            ['edit'],
            ['delete'],
        ];
    }

    /**
     * @return AddDeleteFormListener[]|MockObject[]
     */
    private function createListener(): array
    {
        $formFactory = $this->createMock(AdminFormFactoryInterface::class);

        $listener = new AddDeleteFormListener($formFactory);

        return [$listener, $formFactory];
    }
}
