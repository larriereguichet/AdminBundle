<?php

namespace LAG\AdminBundle\Tests\Factory;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\ActionConfiguration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Admin\View\AdminView;
use LAG\AdminBundle\Factory\FieldFactoryInterface;
use LAG\AdminBundle\Field\StringField;
use LAG\AdminBundle\Routing\Redirection\RedirectionUtils;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\View\Factory\ViewFactory;
use LAG\AdminBundle\View\RedirectView;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ViewFactoryTest extends TestCase
{
    private ViewFactory $viewFactory;
    private MockObject $fieldFactory;
    private MockObject $redirectionUtils;
    private ApplicationConfiguration $applicationConfiguration;

    public function testCreateWithRedirection(): void
    {
        $request = new Request();
        $admin = $this->createMock(AdminInterface::class);

        $this
            ->redirectionUtils
            ->expects($this->once())
            ->method('isRedirectionRequired')
            ->with($admin)
            ->willReturn(true)
        ;
        $this
            ->redirectionUtils
            ->expects($this->once())
            ->method('getRedirectionUrl')
            ->with($admin)
            ->willReturn('my_url')
        ;

        /** @var RedirectView $view */
        $view = $this->viewFactory->create($request, $admin);
        $this->assertInstanceOf(RedirectView::class, $view);
        $this->assertEquals('my_url', $view->getUrl());
    }

    public function testCreate(): void
    {
        $request = new Request();
        $admin = $this->createMock(AdminInterface::class);

        $this
            ->redirectionUtils
            ->expects($this->once())
            ->method('isRedirectionRequired')
            ->with($admin)
            ->willReturn(false)
        ;
        $this
            ->redirectionUtils
            ->expects($this->never())
            ->method('getRedirectionUrl')
        ;

        $actionConfiguration = new ActionConfiguration();
        $actionConfiguration->configure([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'fields' => [
                'name' => [],
            ],
            'path' => '/my-action',
            'route' => 'my-action',
            'template' => '@LAGAdmin/crud/list.html.twig',
        ]);

        $action = $this->createMock(ActionInterface::class);
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $resolver = new OptionsResolver();
        $field = new StringField('title', 'string');
        $field->setApplicationConfiguration($this->applicationConfiguration);
        $field->configureOptions($resolver);
        $field->setOptions($resolver->resolve());

        $this
            ->fieldFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($field)
        ;
        $view = $this->viewFactory->create($request, $admin);
        $this->assertInstanceOf(AdminView::class, $view);
        $this->assertEquals('@LAGAdmin/crud/list.html.twig', $view->getTemplate());
    }

    public function testCreateWithAjax(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
        $admin = $this->createMock(AdminInterface::class);

        $this
            ->redirectionUtils
            ->expects($this->once())
            ->method('isRedirectionRequired')
            ->with($admin)
            ->willReturn(false)
        ;
        $this
            ->redirectionUtils
            ->expects($this->never())
            ->method('getRedirectionUrl')
        ;

        $actionConfiguration = new ActionConfiguration();
        $actionConfiguration->configure([
            'name' => 'my_action',
            'admin_name' => 'my_admin',
            'fields' => [
                'name' => [],
            ],
            'path' => '/my-action',
            'route' => 'my-action',
            'template' => '@LAGAdmin/crud/list.html.twig',
        ]);

        $action = $this->createMock(ActionInterface::class);
        $admin
            ->expects($this->once())
            ->method('getAction')
            ->willReturn($action)
        ;
        $action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($actionConfiguration)
        ;

        $resolver = new OptionsResolver();
        $field = new StringField('title', 'string');
        $field->setApplicationConfiguration($this->applicationConfiguration);
        $field->configureOptions($resolver);
        $field->setOptions($resolver->resolve());

        $this
            ->fieldFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($field)
        ;
        $view = $this->viewFactory->create($request, $admin);
        $this->assertInstanceOf(AdminView::class, $view);
        $this->assertEquals('@LAGAdmin/empty.html.twig', $view->getBase());
    }

    protected function setUp(): void
    {
        $this->fieldFactory = $this->createMock(FieldFactoryInterface::class);
        $this->applicationConfiguration = $this->createApplicationConfiguration([
            'resources_path' => 'my-directory/',
        ]);
        $this->redirectionUtils = $this->createMock(RedirectionUtils::class);

        $this->viewFactory = new ViewFactory(
            $this->fieldFactory,
            $this->applicationConfiguration,
            $this->redirectionUtils
        );
    }
}
