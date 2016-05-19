<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Menu;

use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Factory\AdminFactory;
use LAG\AdminBundle\Menu\MenuBuilder;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilderTest extends AdminTestBase
{
    /**
     * Top and main menus creation SHOULD return an ItemInterface instance.
     */
    public function testMenu()
    {
        $stack = new RequestStack();
        $menuBuilder = new MenuBuilder([
            'main' => [
                'items' => [
                    'test' => [
                        'url' => '#'
                    ],
                    'other_test' => [
                        'url' => '#',
                        'items' => [
                            'test' => [
                                'url' => '#'
                            ],
                        ]
                    ]
                ],
            ]
        ],
            $this->createMenuFactory(),
            $this->createAdminFactory(),
            $stack
        );

        $mainMenu = $menuBuilder->mainMenu();

        $this->assertTrue($mainMenu instanceof ItemInterface);
        $this->assertEquals('main', $mainMenu->getName());
        $this->assertEquals([
            'id' => 'side-menu',
            'class' => 'nav in'
        ], $mainMenu->getChildrenAttributes());

        $topMenu = $menuBuilder->topMenu();

        $this->assertTrue($topMenu instanceof ItemInterface);
        $this->assertEquals('top', $topMenu->getName());


        $stack = new RequestStack();
        $stack->push(new Request([
            '_route_params' => [
                '_admin' => 'test',
                '_action' => 'test',
            ]
        ]));

        /** @var AdminInterface|PHPUnit_Framework_MockObject_MockObject $admin */
        $admin = $this
            ->getMockBuilder(AdminInterface::class)
            ->getMock();
        $actionConfiguration = new ActionConfiguration('test', $admin);
        $actionConfiguration->setParameters([
            'title' => 'Test',
            'permissions' => [],
            'menus' => []
        ]);

        $admin
            ->method('getCurrentAction')
            ->willReturn(new Action('test', $actionConfiguration));
        $admin
            ->method('isCurrentActionDefined')
            ->willReturn(true);

        /** @var AdminFactory|PHPUnit_Framework_MockObject_MockObject $adminFactory */
        $adminFactory = $this
            ->getMockBuilder(AdminFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adminFactory
            ->method('getAdminFromRequest')
            ->willReturn($admin);

        $menuBuilder = new MenuBuilder(
            [],
            $this->createMenuFactory(),
            $adminFactory,
            $stack
        );

        $topMenu = $menuBuilder->topMenu();
        $this->assertTrue($topMenu instanceof ItemInterface);
        $this->assertEquals('top', $topMenu->getName());
        $this->assertEquals('nav navbar-top-links navbar-right in', $topMenu->getChildrenAttributes()['class']);
    }
}
