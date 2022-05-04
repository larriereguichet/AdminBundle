<?php

namespace LAG\AdminBundle\Tests\Debug\Collector;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Admin\Resource\AdminResource;
use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugCollectorTest extends TestCase
{
    public function testCollector(): void
    {
        // Several methods are tested in this test as testing some methods as getData() or reset() without data is
        // useless
        [$collector, $registry, $storage,] = $this->createCollector();

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

        $storage
            ->expects($this->once())
            ->method('isFrozen')
            ->willReturn(true)
        ;

        $collector->collect($request, $response);
        $data = $collector->getData();

        $this->assertEquals([
            'admins' => [
                'pandas' => [
                    'entity_class' => 'AdminPanda',
                    'configuration' => [
                        'admin ' => 'bamboo',
                    ],
                ],
            ],
            'application' => [
                'admin' => 'panda',
                'action' => 'bamboo',
            ],
        ], $data);
    }

    /**
     * @return AdminDataCollector[]|MockObject[]
     */
    private function createCollector(): array
    {
        $registry = $this->createMock(ResourceRegistryInterface::class);
        $applicationConfiguration = $this->createMock(ApplicationConfiguration::class);

        $collector = new AdminDataCollector($registry, $applicationConfiguration, []);

        return [
            $collector,
            $registry,
            $applicationConfiguration,
        ];
    }
}
