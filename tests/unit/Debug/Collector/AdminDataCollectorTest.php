<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Debug\Collector;

use LAG\AdminBundle\Debug\DataCollector\AdminDataCollector;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminDataCollectorTest extends TestCase
{
    private AdminDataCollector $collector;
    private MockObject $resourceContext;

    #[Test]
    public function itCollectsDebugData(): void
    {
        $request = new Request();
        $response = new Response();

        $resource = new Resource(shortName: 'my_resource', application: 'my_application');
        $operation = new Show(name: 'my_operation')->setResource($resource);

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(true)
        ;
        $this->resourceContext
            ->expects($this->once())
            ->method('getOperation')
            ->willReturn($operation)
        ;

        $this->collector->collect($request, $response);

        self::assertEquals([
            'application' => 'my_application',
            'resource' => 'my_application.my_resource',
            'operation' => 'my_operation',
        ], $this->collector->getData());

        $this->collector->reset();

        self::assertEquals([], $this->collector->getData());
    }

    #[Test]
    public function itReturnsCollectionName(): void
    {
        $this->resourceContext->expects($this->never())->method('hasOperation');
        self::assertEquals(AdminDataCollector::class, $this->collector->getName());
    }

    #[Test]
    public function itDoesNotCollectWhenNoOperation(): void
    {
        $this->resourceContext->method('hasOperation')->willReturn(false);
        $this->resourceContext->expects($this->never())->method('getOperation');

        $this->collector->collect(new Request(), new Response());

        self::assertSame([], $this->collector->getData());
    }

    #[Test]
    public function itReturnsTemplate(): void
    {
        self::assertSame('@LAGAdmin/debug/template.html.twig', AdminDataCollector::getTemplate());
    }

    protected function setUp(): void
    {
        $this->resourceContext = $this->createMock(ResourceContextInterface::class);
        $this->collector = new AdminDataCollector(
            $this->resourceContext,
        );
    }
}
