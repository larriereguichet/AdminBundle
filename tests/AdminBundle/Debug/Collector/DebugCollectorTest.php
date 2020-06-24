<?php

namespace LAG\AdminBundle\Tests\Debug\Collector;

use Knp\Menu\ItemInterface;
use LAG\AdminBundle\Bridge\KnpMenu\Provider\MenuProvider;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;
use LAG\AdminBundle\Resource\AdminResource;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Tests\AdminTestBase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugCollectorTest extends AdminTestBase
{
    public function testCollector(): void
    {
        // Several methods are tested in this test as testing some methods as getData() or reset() without data is
        // useless
        list($collector, $registry, $storage, $menuProvider) = $this->createCollector();

        $this->assertEquals('admin.data_collector', $collector->getName());
        $this->assertEquals([], $collector->getData());

        // Reset method do nothing with empty data
        $collector->reset();
        $this->assertEquals([], $collector->getData());

        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag();
        $request->attributes->set('_admin', 'panda');
        $request->attributes->set('_action', 'bamboo');

        $response = $this->createMock(Response::class);

        $resource = $this->createMock(AdminResource::class);
        $resource
            ->expects($this->once())
            ->method('getEntityClass')
            ->willReturn('AdminPanda')
        ;
        $resource
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([
                'admin ' => 'bamboo',
            ])
        ;
        $resource
            ->expects($this->once())
            ->method('getName')
            ->willReturn('pandas')
        ;

        $registry
            ->expects($this->once())
            ->method('all')
            ->willReturn([
                $resource,
            ])
        ;

        $configuration = $this->createMock(ApplicationConfiguration::class);

        $configuration
            ->expects($this->once())
            ->method('all')
            ->willReturn([
                'leaves' => ['shoot'],
                'eat' => true,
            ])
        ;

        $storage
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;
        $storage
            ->expects($this->once())
            ->method('isFrozen')
            ->willReturn(true)
        ;

        $child = $this->createMock(ItemInterface::class);

        $menu = $this->createMock(ItemInterface::class);
        $menu
            ->expects($this->once())
            ->method('getChildren')
            ->willReturn([
                'grizzly' => $child,
            ])
        ;

        $menuProvider
            ->expects($this->once())
            ->method('all')
            ->willReturn(['bears' => $menu])
        ;

        $collector->collect($request, $response);
        $data = $collector->getData();

        $this->assertEquals([
            'admins' => [
                'pandas' => [
                    'entity_class' => 'AdminPanda',
                    'configuration' => [
                        'admin ' => 'bamboo'
                    ],
                ],
            ],
            'application' => [
                'leaves' => ['shoot'],
                'eat' => true,
                'admin' => 'panda',
                'action' => 'bamboo',
            ],
            'menus' => [
                'bears' => [
                    'attributes' => [],
                    'displayed' => false,
                    'children' => [
                        'grizzly' => [
                            'uri' => null,
                            'attributes' => [],
                            'displayed' => false,
                        ]
                    ]
                ]
            ],

        ], $data);
    }

    /**
     * @return AdminDataCollector[]|MockObject[]
     */
    private function createCollector(): array
    {
        $registry = $this->createMock(ResourceRegistryInterface::class);
        $storage = $this->createMock(ApplicationConfigurationStorage::class);
        $menuProvider = $this->createMock(MenuProvider::class);

        $collector = new AdminDataCollector($registry, $storage, $menuProvider);

        return [
            $collector,
            $registry,
            $storage,
            $menuProvider
        ];
    }
}
