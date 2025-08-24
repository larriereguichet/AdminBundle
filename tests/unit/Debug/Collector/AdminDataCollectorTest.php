<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Debug\Collector;

use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Resource\Context\OperationContextInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminDataCollectorTest extends TestCase
{
    private AdminDataCollector $collector;
    private MockObject $operationContext;

    #[Test]
    public function itCollectsDebugData(): void
    {
        $request = new Request();
        $response = new Response();

        $resource = new Resource(name: 'my_resource', application: 'my_application');
        $operation = new Show(name: 'my_operation')->setResource($resource);

        $this->operationContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(true)
        ;
        $this->operationContext
            ->expects($this->once())
            ->method('getOperation')
            ->willReturn($operation)
        ;

        $this->collector->collect($request, $response);

        self::assertEquals([
            'application' => 'my_application',
            'resource' => 'my_resource',
            'operation' => 'my_operation',
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
        $this->operationContext = $this->createMock(OperationContextInterface::class);
        $this->collector = new AdminDataCollector(
            $this->operationContext,
        );
    }
}
