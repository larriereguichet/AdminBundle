<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Menu;

use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Action;
use LAG\AdminBundle\Action\ListAction;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Registry\Registry;
use LAG\AdminBundle\Admin\Request\RequestHandler;
use LAG\AdminBundle\Menu\MenuBuilder;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilderTest extends AdminTestBase
{
//    /**
//     * Top and main menus creation SHOULD return an ItemInterface instance.
//     */
//    public function testMenu()
//    {
//        $requestStack = new RequestStack();
//        $mainMenuConfiguration = [
//            'main' => [
//                'items' => [
//                    'test' => [
//                        'url' => '#'
//                    ],
//                    'other_test' => [
//                        'url' => '#',
//                        'items' => [
//                            'test' => [
//                                'url' => '#'
//                            ],
//                        ]
//                    ]
//                ],
//            ]
//        ];
//        $menuFactory = $this->createMenuFactory();
//        $registry = new Registry();
//
//        // create menu builder
//        $menuBuilder = new MenuBuilder(
//            $mainMenuConfiguration,
//            $menuFactory,
//            new RequestHandler($registry),
//            $requestStack
//        );
//
//        // main menu SHOULD be a valid menu item instance
//        $mainMenu = $menuBuilder->mainMenu();
//        $this->assertTrue($mainMenu instanceof ItemInterface);
//        $this->assertEquals('main', $mainMenu->getName());
//        $this->assertEquals([
//            'id' => 'side-menu',
//            'class' => 'nav in'
//        ], $mainMenu->getChildrenAttributes());
//
//        // top menu SHOULD be a valid menu item instance
//        $topMenu = $menuBuilder->topMenu();
//        $this->assertTrue($topMenu instanceof ItemInterface);
//        $this->assertEquals('top', $topMenu->getName());
//
//
//        $requestStack->push(new Request([
//            '_route_params' => [
//                '_admin' => 'test',
//                '_action' => 'test',
//            ]
//        ]));
//
//        /** @var AdminInterface|PHPUnit_Framework_MockObject_MockObject $admin */
//        $admin = $this
//            ->getMockBuilder(AdminInterface::class)
//            ->getMock();
//        $actionConfiguration = new ActionConfiguration('test', $admin);
//        $actionConfiguration->setParameters([
//            'title' => 'Test',
//            'permissions' => [],
//            'menus' => []
//        ]);
//
//        $action = $this->getMockWithoutConstructor(ActionInterface::class);
//        $action
//            ->expects($this->once())
//            ->method('getConfiguration')
//            ->willReturn($actionConfiguration)
//        ;
//
//        $admin
//            ->method('getName')
//            ->willReturn('test');
//        $admin
//            ->method('getCurrentAction')
//            ->willReturn($action);
//        $admin
//            ->method('isCurrentActionDefined')
//            ->willReturn(true);
//
//        $menuBuilder = new MenuBuilder(
//            [],
//            $this->createMenuFactory(),
//            new RequestHandler($registry),
//            $requestStack
//        );
//        $registry->add($admin);
//
//        $topMenu = $menuBuilder->topMenu();
//        $this->assertTrue($topMenu instanceof ItemInterface);
//        $this->assertEquals('top', $topMenu->getName());
//        $this->assertEquals('nav navbar-top-links navbar-right in', $topMenu->getChildrenAttributes()['class']);
//    }
}
