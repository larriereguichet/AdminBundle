<?php

namespace LAG\AdminBundle\Tests\Action\Factory;

use LAG\AdminBundle\Action\Factory\ActionConfigurationFactory;
use LAG\AdminBundle\Action\Factory\ActionConfigurationFactoryInterface;
use LAG\AdminBundle\Controller\AdminAction;
use LAG\AdminBundle\Event\Events\Configuration\ActionConfigurationEvent;
use LAG\AdminBundle\Exception\Action\ActionConfigurationException;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ActionConfigurationFactoryTest extends TestCase
{
    private ActionConfigurationFactoryInterface $factory;

    private MockObject $eventDispatcher;

    /** @dataProvider createDataProvider */
    public function testCreate(
        string $adminName,
        string $actionName,
        array $options,
        array $expectedConfiguration
    ): void {
        $expectedOptions = array_merge([
            'admin_name' => $adminName,
            'name' => $actionName,
        ], $options);
        $this
            ->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(new ActionConfigurationEvent($adminName, $actionName, $expectedOptions))
        ;
        $configuration = $this->factory->create($adminName, $actionName, $options);

        $this->assertEquals($expectedConfiguration, $configuration->toArray());
    }

    protected function createDataProvider(): array
    {
        return [
            [
                'my_admin',
                'my_action',
                [
                    'route' => 'my_admin.my_action',
                    'template' => 'test.html.twig',
                ],
                [
                    'name' => 'my_action',
                    'admin_name' => 'my_admin',
                    'title' => 'MyAction',
                    'icon' => null,
                    'action_class' => Action::class,
                    'template' => 'test.html.twig',
                    'permissions' => ['ROLE_ADMIN'],
                    'controller' => AdminAction::class,
                    'route_parameters' => [],
                    'load_strategy' => 'strategy_unique',
                    'path' => 'my-admins/{id}/my-action',
                    'target_route' => 'index',
                    'target_route_parameters' => [],
                    'fields' => [],
                    'order' => [],
                    'criteria' => [],
                    'filters' => [],
                    'repository_method' => null,
                    'pager' => 'pagerfanta',
                    'max_per_page' => 25,
                    'page_parameter' => 'page',
                    'date_format' => 'Y-m-d',
                    'form' => null,
                    'form_options' => [],
                    'route' => 'my_admin.my_action',
                ],
            ],
            [
                'my_admin',
                'index',
                [
                    'route' => 'my_admin.my_action',
                ],
                [
                    'name' => 'index',
                    'admin_name' => 'my_admin',
                    'title' => 'Index',
                    'icon' => null,
                    'action_class' => Action::class,
                    'template' => '@LAGAdmin/crud/list.html.twig',
                    'permissions' => ['ROLE_ADMIN'],
                    'controller' => AdminAction::class,
                    'route_parameters' => [],
                    'load_strategy' => 'strategy_multiple',
                    'path' => 'my-admins',
                    'target_route' => 'index',
                    'target_route_parameters' => [],
                    'fields' => [],
                    'order' => [],
                    'criteria' => [],
                    'filters' => [],
                    'repository_method' => null,
                    'pager' => 'pagerfanta',
                    'max_per_page' => 25,
                    'page_parameter' => 'page',
                    'date_format' => 'Y-m-d',
                    'form' => null,
                    'form_options' => [],
                    'route' => 'my_admin.my_action',
                ],
            ],
        ];
    }

    public function testCreateWithInvalidConfiguration(): void
    {
        $this->expectException(ActionConfigurationException::class);
        $this->factory->create('an_admin', 'an_action', []);
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->factory = new ActionConfigurationFactory($this->eventDispatcher);
    }
}
