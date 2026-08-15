<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Twig\Globals;

use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Twig\Globals\LAGAdminGlobal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LAGAdminGlobalTest extends TestCase
{
    private LAGAdminGlobal $adminContext;
    private MockObject $resourceContext;

    #[Test]
    public function itReturnsTheCurrentResource(): void
    {
        $expectedResource = new Resource(shortName: 'my_resource');

        $this->resourceContext
            ->expects($this->once())
            ->method('hasResource')
            ->willReturn(true)
        ;
        $this->resourceContext
            ->expects($this->once())
            ->method('getResource')
            ->willReturn($expectedResource)
        ;

        $resource = $this->adminContext->getResource();

        self::assertEquals($expectedResource, $resource);
    }

    #[Test]
    public function itReturnsTheCurrentOperation(): void
    {
        $expectedOperation = new Index(name: 'my_operation');

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(true)
        ;
        $this->resourceContext
            ->expects($this->once())
            ->method('getOperation')
            ->willReturn($expectedOperation)
        ;

        $operation = $this->adminContext->getOperation();

        self::assertEquals($expectedOperation, $operation);
    }

    #[Test]
    public function itReturnsNullWhenNoResource(): void
    {
        $this->resourceContext->method('hasResource')->willReturn(false);
        $this->resourceContext->expects($this->never())->method('getResource');

        self::assertNull($this->adminContext->getResource());
    }

    #[Test]
    public function itReturnsNullWhenNoOperation(): void
    {
        $this->resourceContext->method('hasOperation')->willReturn(false);
        $this->resourceContext->expects($this->never())->method('getOperation');

        self::assertNull($this->adminContext->getOperation());
    }

    protected function setUp(): void
    {
        $this->resourceContext = $this->createMock(ResourceContextInterface::class);
        $this->adminContext = new LAGAdminGlobal($this->resourceContext);
    }
}
