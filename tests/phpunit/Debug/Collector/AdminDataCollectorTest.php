<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Debug\Collector;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
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
    private MockObject $applicationConfiguration;
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
            ->applicationConfiguration
            ->expects($this->once())
            ->method('isFrozen')
            ->willReturn(true)
        ;
        $this
            ->applicationConfiguration
            ->expects($this->once())
            ->method('toArray')
            ->willReturn([
                'resource_paths' => '/path',
                'title' => 'A Title',
                'date_format' => 'd/m/Y',
                'time_format' => null,
                'date_localization' => false,
            ])
        ;

        $this
            ->parametersExtractor
            ->expects($this->once())
            ->method('supports')
            ->willReturn(true)
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
                'resource' => 'my_resource',
                'operation' => 'my_operation',
            ],
            'application' => [
                'resource_paths' => '/path',
                'title' => 'A Title',
                'date_format' => 'd/m/Y',
                'time_format' => null,
                'date_localization' => false,
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
        $this->applicationConfiguration = $this->createMock(ApplicationConfiguration::class);
        $this->parametersExtractor = $this->createMock(ParametersExtractorInterface::class);
        $this->collector = new AdminDataCollector(
            $this->registry,
            $this->applicationConfiguration,
            $this->parametersExtractor,
        );
    }
}
