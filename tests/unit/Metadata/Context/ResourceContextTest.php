<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Context;

use LAG\AdminBundle\Exception\UnsupportedRequestException;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Resource\Context\ResourceContext;
use LAG\AdminBundle\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ResourceContextTest extends TestCase
{
    private ResourceContext $resourceContext;

    #[Test]
    public function itReturnsTheCurrentResource(): void
    {
        $resource = new Resource(shortName: 'my_resource');
        $this->resourceContext->setResource($resource);

        $contextResource = $this->resourceContext->getResource();

        $this->assertEquals($resource->getShortName(), $contextResource->getShortName());
    }

    #[Test]
    public function itDoesNotReturnAMissingResource(): void
    {
        $this->expectException(UnsupportedRequestException::class);
        $this->resourceContext->getResource();
    }

    #[Test]
    public function itCheckIfThereIsACurrentResource(): void
    {
        $this->assertFalse($this->resourceContext->hasResource());

        $resource = new Resource(shortName: 'my_resource');
        $this->resourceContext->setResource($resource);

        $this->assertTrue($this->resourceContext->hasResource());
    }

    #[Test]
    public function itReturnsTheCurrentOperation(): void
    {
        $operation = new Show(name: 'my_operation');
        $this->resourceContext->setOperation($operation);

        $this->assertEquals($operation, $this->resourceContext->getOperation());
    }

    #[Test]
    public function itDoesNotReturnAMissingOperation(): void
    {
        $this->expectException(UnsupportedRequestException::class);
        $this->resourceContext->getOperation();
    }

    #[Test]
    public function itPopsTheStack(): void
    {
        $resource = new Resource(shortName: 'my_resource');
        $this->resourceContext->setResource($resource);
        $this->resourceContext->pop();

        $this->assertFalse($this->resourceContext->hasResource());
    }

    #[Test]
    public function itThrowsWhenSettingOperationTwice(): void
    {
        $this->expectException(\LAG\AdminBundle\Exception\Exception::class);

        $operation = new Show(name: 'my_operation');
        $this->resourceContext->setOperation($operation);
        $this->resourceContext->setOperation($operation);
    }

    #[Test]
    public function itCheckIfThereIsACurrentOperation(): void
    {
        $this->assertFalse($this->resourceContext->hasOperation());

        $operation = new Show(name: 'my_operation');
        $this->resourceContext->setOperation($operation);

        $this->assertTrue($this->resourceContext->hasOperation());
    }

    protected function setUp(): void
    {
        $this->resourceContext = new ResourceContext();
        $this->resourceContext->push();
    }
}
