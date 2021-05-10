<?php

namespace LAG\AdminBundle\Tests\Event\Listener\Form;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\DataPersister\DataPersisterInterface;
use LAG\AdminBundle\DataPersister\Registry\DataPersisterRegistryInterface;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Event\Listener\Form\PersistDataListener;
use LAG\AdminBundle\Session\FlashMessage\FlashMessageHelperInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\Form\FormInterface;

class PersistDataListenerTest extends TestCase
{
    private MockObject $registry;
    private MockObject $flashHelper;
    private PersistDataListener $listener;

    public function testInvoke(): void
    {
        $event = $this->createMock(FormEvent::class);

        $admin = $this->createMock(AdminInterface::class);
        $event
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;

        $admin
            ->expects($this->once())
            ->method('hasForm')
            ->with('entity')
            ->willReturn(true)
        ;

        $form = $this->createMock(FormInterface::class);
        $admin
            ->expects($this->once())
            ->method('getForm')
            ->with('entity')
            ->willReturn($form)
        ;

        $form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;

        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $admin
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
        $adminConfiguration
            ->expects($this->once())
            ->method('getDataPersister')
            ->willReturn('my_data_persister')
        ;

        $persister = $this->createMock(DataPersisterInterface::class);
        $this->registry
            ->expects($this->once())
            ->method('get')
            ->with('my_data_persister')
            ->willReturn($persister)
        ;

        $data = new stdClass();
        $admin
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data)
        ;
        $persister
            ->expects($this->once())
            ->method('save')
            ->with($data)
        ;

        $this->listener->__invoke($event);
    }

    public function testInvokeWithoutEntityForm(): void
    {
        $event = $this->createMock(FormEvent::class);

        $admin = $this->createMock(AdminInterface::class);
        $event
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;

        $admin
            ->expects($this->once())
            ->method('hasForm')
            ->with('entity')
            ->willReturn(false)
        ;

        $admin
            ->expects($this->never())
            ->method('getForm')
        ;

        $this->listener->__invoke($event);
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(DataPersisterRegistryInterface::class);
        $this->flashHelper = $this->createMock(FlashMessageHelperInterface::class);
        $this->listener = new PersistDataListener($this->registry, $this->flashHelper);
    }
}
