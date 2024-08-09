<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Debug\Collector;

use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;
use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractorInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminDataCollectorTest extends TestCase
{
    private AdminDataCollector $collector;
    private MockObject $registry;
    private MockObject $parametersExtractor;

    #[Test]
    public function itCollectsDebugData(): void
    {
        $request = new Request();
        $response = new Response();

        $resource1 = new Resource(name: 'my_resource');
        $resource2 = new Resource(name: 'my_other_resource');

        $this
            ->registry
            ->expects(self::once())
            ->method('all')
            ->willReturn([$resource1, $resource2])
        ;

        $this
            ->parametersExtractor
            ->expects(self::once())
            ->method('getApplicationName')
            ->willReturn('admin')
        ;
        $this
            ->parametersExtractor
            ->expects(self::once())
            ->method('getResourceName')
            ->with($request)
            ->willReturn('my_resource')
        ;
        $this
            ->parametersExtractor
            ->expects(self::once())
            ->method('getOperationName')
            ->with($request)
            ->willReturn('my_operation')
        ;

        $this->collector->collect($request, $response);

        self::assertEquals([
            'application' => 'admin',
            'resource' => 'my_resource',
            'operation' => 'my_operation',
            'resources' => ['my_resource' => $resource1, 'my_other_resource' => $resource2],
        ], $this->collector->getData());

        $this->collector->reset();
        self::assertEquals([], $this->collector->getData());
    }

    #[Test]
    public function itReturnsCollectionName(): void
    {
        self::assertEquals(AdminDataCollector::class, $this->collector->getName());
    }

    protected function setUp(): void
    {
        $this->registry = self::createMock(ResourceRegistryInterface::class);
        $this->parametersExtractor = self::createMock(ResourceParametersExtractorInterface::class);
        $this->collector = new AdminDataCollector(
            $this->registry,
            $this->parametersExtractor,
        );
    }
}
