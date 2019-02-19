<?php

namespace LAG\AdminBundle\Tests\Event\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Event\Subscriber\FormSubscriber;
use LAG\AdminBundle\Factory\DataProviderFactory;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Fixtures\FakeEntity;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class FormSubscriberTest extends AdminTestBase
{
    public function testServiceExists()
    {
        $this->assertServiceExists(FormSubscriber::class);
    }

    public function testMethodsExists()
    {
        list($subscriber) = $this->createSubscriber();

        $this->assertSubscribedMethodsExists($subscriber);
    }

    public function testOnCreateForm()
    {
        list($subscriber, $formFactory, $dataProviderFactory) = $this->createSubscriber();

        $entity = new FakeEntity();

        $dataProvider = $this->createMock(DataProviderInterface::class);
        $dataProvider
            ->expects($this->once())
            ->method('create')
            ->willReturn($entity)
        ;

        $dataProviderFactory
            ->expects($this->once())
            ->method('get')
            ->with('my_little_provider')
            ->willReturn($dataProvider)
        ;

        $form = $this->createMock(FormInterface::class);
        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with('my_little_form', $entity)
            ->willReturn($form)
        ;

        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['use_form', true],
                ['load_strategy', LAGAdminBundle::LOAD_STRATEGY_UNIQUE],
            ])
        ;
        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('create')
        ;
        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['data_provider', 'my_little_provider'],
                ['form', 'my_little_form'],
            ])
        ;
        $entities = new ArrayCollection();
        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->atLeastOnce())
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
        $admin
            ->expects($this->exactly(1))
            ->method('getEntities')
            ->willReturn($entities)
        ;

        $request = new Request();
        $event = new FormEvent($admin, $request);
        $subscriber->onCreateForm($event);
    }

    public function testOnCreateFormWithoutForm()
    {
        list($subscriber) = $this->createSubscriber();

        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->once())
            ->method('get')
            ->willReturnMap([
                ['use_form', false],
            ])
        ;

        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;

        $request = new Request();

        $event = new FormEvent($admin, $request);

        $subscriber->onCreateForm($event);
    }

    public function testOnCreateFormWithEntities()
    {
        list($subscriber, $formFactory) = $this->createSubscriber();

        $entity = new FakeEntity();

        $form = $this->createMock(FormInterface::class);
        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with('my_little_form', $entity)
            ->willReturn($form)
        ;

        $actionConfiguration = $this->createMock(ActionConfiguration::class);
        $actionConfiguration
            ->expects($this->exactly(3))
            ->method('get')
            ->willReturnMap([
                ['use_form', true],
                ['load_strategy', LAGAdminBundle::LOAD_STRATEGY_UNIQUE],
                ['form', 'my_little_form'],
            ])
        ;
        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('delete')
        ;
        $action
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;
        $entities = new ArrayCollection();
        $entities->add(new FakeEntity());

        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->exactly(2))
            ->method('getEntities')
            ->willReturn($entities)
        ;

        $request = new Request();
        $event = new FormEvent($admin, $request);
        $subscriber->onCreateForm($event);
    }

    public function testOnHandleForm()
    {
        list($subscriber,, $dataProviderFactory, $session) = $this->createSubscriber();

        $request = new Request();

        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getName')
            ->willReturn('delete')
        ;
        $form = $this->createMock(FormInterface::class);
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
        $form
            ->expects($this->once())
            ->method('handleRequest')
            ->willReturn($request)
        ;
        $adminConfiguration = $this->createMock(AdminConfiguration::class);
        $adminConfiguration
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['data_provider', 'my_little_provider'],
                ['form', 'my_delete_form'],
                ['translation_pattern', 'a pattern'],
            ])
        ;

        // The subscriber should call the delete form from the admin
        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getName')
            ->willReturn('my_little_admin')
        ;
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->once())
            ->method('hasForm')
            ->with('delete')
            ->willReturn(true)
        ;
        $admin
            ->expects($this->once())
            ->method('getForm')
            ->with('delete')
            ->willReturn($form)
        ;
        $admin
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn($adminConfiguration)
        ;
        $dataProvider = $this->createMock(DataProviderInterface::class);
        $dataProvider
            ->expects($this->once())
            ->method('delete')
            ->with($admin)
        ;
        $dataProviderFactory
            ->expects($this->once())
            ->method('get')
            ->with('my_little_provider')
            ->willReturn($dataProvider)
        ;

        // A flash message should be added to the session bag
        $flashBag = $this->createMock(FlashBag::class);
        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with('success')
        ;
        $session
            ->expects($this->once())
            ->method('getFlashBag')
            ->willReturn($flashBag)
        ;

        $event = new FormEvent($admin, $request);
        $subscriber->onHandleForm($event);
    }

    public function testOnHandleFormWithoutForm()
    {
        list($subscriber) = $this->createSubscriber();

        $request = new Request();

        $action = $this->createMock(ActionInterface::class);
        $action
            ->expects($this->once())
            ->method('getName')
            ->willReturn('delete')
        ;
        $admin = $this->createMock(AdminInterface::class);
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $admin
            ->expects($this->once())
            ->method('hasForm')
            ->with('delete')
            ->willReturn(false)
        ;

        $event = new FormEvent($admin, $request);

        $subscriber->onHandleForm($event);
    }

    /**
     * @return FormSubscriber[]|MockObject[]
     */
    private function createSubscriber()
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $dataProviderFactory = $this->createMock(DataProviderFactory::class);
        $session = $this->createMock(Session::class);
        $translator = $this->createMock(TranslatorInterface::class);

        $subscriber = new FormSubscriber($formFactory, $dataProviderFactory, $session, $translator);

        return [
            $subscriber,
            $formFactory,
            $dataProviderFactory,
            $session,
        ];
    }
}
