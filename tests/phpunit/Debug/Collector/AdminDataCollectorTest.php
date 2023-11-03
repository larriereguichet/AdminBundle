<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Debug\Collector;

use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminDataCollectorTest extends TestCase
{
    private AdminDataCollector $collector;
    private MockObject $registry;
    private MockObject $parametersExtractor;

    public function testCollect(): void
    {
        $request = new Request();
        $response = new Response();

        $resource1 = new AdminResource(name: 'my_resource');
        $resource2 = new AdminResource(name: 'my_other_resource');

        $this
            ->registry
            ->expects($this->once())
            ->method('all')
            ->willReturn([$resource1, $resource2])
        ;

        $this
            ->parametersExtractor
            ->expects($this->once())
            ->method('getApplicationName')
            ->willReturn('admin')
        ;
        $this
            ->parametersExtractor
            ->expects($this->once())
            ->method('getResourceName')
            ->with($request)
            ->willReturn('my_resource')
        ;
        $this
            ->parametersExtractor
            ->expects($this->once())
            ->method('getOperationName')
            ->with($request)
            ->willReturn('my_operation')
        ;

        $this->collector->collect($request, $response);

        $this->assertEquals([
            'resources' => [$resource1, $resource2],
            'request' => [
                'application' => 'admin',
                'resource' => 'my_resource',
                'operation' => 'my_operation',
            ],
            'application' => [
                'param' => 'a value',
            ],
        ], $this->collector->getData());

        $this->collector->reset();
        $this->assertEquals([], $this->collector->getData());
    }

    public function testGetName(): void
    {
        $this->assertEquals(AdminDataCollector::class, $this->collector->getName());
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ResourceRegistryInterface::class);
        $this->parametersExtractor = $this->createMock(ParametersExtractorInterface::class);
        $this->collector = new AdminDataCollector(
            ['param' => 'a value'],
            $this->registry,
            $this->parametersExtractor,
        );
    }
}
