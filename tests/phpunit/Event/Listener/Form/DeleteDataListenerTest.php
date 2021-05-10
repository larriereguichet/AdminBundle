<?php

namespace LAG\AdminBundle\Tests\Event\Listener\Form;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\DataPersister\DataPersisterInterface;
use LAG\AdminBundle\DataPersister\Registry\DataPersisterRegistryInterface;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Event\Listener\Form\DeleteDataListener;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class DeleteDataListenerTest extends TestCase
{
    public function testInvoke(): void
    {
        [$listener, $registry, $session] = $this->createListener();

        $event = $this->createMock(FormEvent::class);

        $admin = $this->createMock(AdminInterface::class);
        $configuration = $this->createMock(AdminConfiguration::class);

        $event
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;
        $admin
            ->expects($this->once())
            ->method('hasForm')
            ->with('delete')
            ->willReturn(true)
        ;

        $deleteForm = $this->createMock(FormInterface::class);
        $admin
            ->expects($this->once())
            ->method('getForm')
            ->with('delete')
            ->willReturn($deleteForm)
        ;

        $deleteForm
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true)
        ;
        $deleteForm
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true)
        ;

        $configuration
            ->expects($this->once())
            ->method('getDataPersister')
            ->willReturn('my_data_persister')
        ;

        $dataPersister = $this->createMock(DataPersisterInterface::class);
        $registry
            ->expects($this->once())
            ->method('get')
            ->with('my_data_persister')
            ->willReturn($dataPersister)
        ;

        $data = new stdClass();

        $admin
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data)
        ;
        $dataPersister
            ->expects($this->once())
            ->method('delete')
            ->with()
        ;

        $configuration
            ->expects($this->once())
            ->method('isTranslationEnabled')
            ->willReturn(true)
        ;
        $configuration
            ->expects($this->once())
            ->method('getTranslationPattern')
            ->willReturn('lag.{admin}.{key}')
        ;
        $admin
            ->expects($this->once())
            ->method('getName')
            ->willReturn('my_admin')
        ;

        $flashBag = $this->createMock(FlashBagInterface::class);
        $session
            ->expects($this->once())
            ->method('getFlashBag')
            ->willReturn($flashBag)
        ;
        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with('success', 'lag.my_admin.deleted')
        ;

        $listener->__invoke($event);
    }

    public function testInvokeWithoutForm(): void
    {
        [$listener] = $this->createListener();

        $event = $this->createMock(FormEvent::class);

        $admin = $this->createMock(AdminInterface::class);
        $configuration = $this->createMock(AdminConfiguration::class);

        $admin
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;
        $event
            ->expects($this->once())
            ->method('getAdmin')
            ->willReturn($admin)
        ;
        $admin
            ->expects($this->once())
            ->method('hasForm')
            ->with('delete')
            ->willReturn(false)
        ;

        $listener->__invoke($event);
    }

    /**
     * @return DeleteDataListener[]|MockObject[]
     */
    private function createListener(): array
    {
        $registry = $this->createMock(DataPersisterRegistryInterface::class);
        $session = $this->createMock(Session::class);

        $listener = new DeleteDataListener($registry, $session);

        return [
            $listener,
            $registry,
            $session,
        ];
    }
}
