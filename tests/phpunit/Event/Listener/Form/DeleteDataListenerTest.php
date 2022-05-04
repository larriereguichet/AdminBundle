<?php

namespace LAG\AdminBundle\Tests\Event\Listener\Form;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
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
    private DeleteDataListener $listener;
    private ApplicationConfiguration $applicationConfiguration;
    private MockObject $session;
    private MockObject $registry;

    public function testInvoke(): void
    {
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
        $this
            ->registry
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

        $admin
            ->expects($this->once())
            ->method('getName')
            ->willReturn('my_admin')
        ;

        $flashBag = $this->createMock(FlashBagInterface::class);
        $this
            ->session
            ->expects($this->once())
            ->method('getFlashBag')
            ->willReturn($flashBag)
        ;
        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with('success', 'admin.my_admin.deleted')
        ;

        $this->listener->__invoke($event);
    }

    public function testInvokeWithoutForm(): void
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
            ->with('delete')
            ->willReturn(false)
        ;

        $this->listener->__invoke($event);
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(DataPersisterRegistryInterface::class);
        $this->session = $this->createMock(Session::class);
        $this->applicationConfiguration = $this->createApplicationConfiguration([
            'resources_path' => __DIR__,
            'translation' => ['enabled' => true],
        ]);

        $this->listener = new DeleteDataListener($this->registry, $this->session, $this->applicationConfiguration);
    }
}
