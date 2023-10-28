<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Context;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Context\ResourceContext;
use LAG\AdminBundle\Metadata\Get;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;

class ResourceContextTest extends TestCase
{
    private ResourceContext $resourceContext;
    private MockObject $parametersExtractor;
    private MockObject $resourceRegistry;

    public function testGet(): void
    {
        $request = new Request(['test']);

        $resource = new AdminResource(name: 'my_resource');
        $operation = new Get(name: 'my_operation');
        $operation = $operation->withResource($resource);
        $resource = $resource->withOperations([$operation]);

        $this
            ->parametersExtractor
            ->expects($this->once())
            ->method('supports')
            ->with($request)
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

        $this
            ->resourceRegistry
            ->expects($this->once())
            ->method('get')
            ->with('my_resource')
            ->willReturn($resource)
        ;

        $contextResource = $this->resourceContext->getResource($request);
        $this->assertEquals($resource->getName(), $contextResource->getName());
    }

    public function testSupports(): void
    {
        $request = new Request(['test']);

        $this
            ->parametersExtractor
            ->expects($this->once())
            ->method('supports')
            ->with($request)
            ->willReturn(true)
        ;

        $this->resourceContext->supports($request);
    }

    public function testGetWithoutSupport(): void
    {
        $request = new Request(['test']);

        $this
            ->parametersExtractor
            ->expects($this->once())
            ->method('supports')
            ->with($request)
            ->willReturn(false)
        ;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The current request is not supported by any admin resource');
        $this->resourceContext->getOperation($request);
    }

    protected function setUp(): void
    {
        $this->parametersExtractor = $this->createMock(ParametersExtractorInterface::class);
        $this->resourceRegistry = $this->createMock(ResourceRegistryInterface::class);
        $this->resourceContext = new ResourceContext(
            $this->parametersExtractor,
            $this->resourceRegistry,
        );
    }
}
