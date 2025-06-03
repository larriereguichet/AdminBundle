<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Context;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Context\ResourceContext;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ResourceContextTest extends TestCase
{
    private ResourceContext $resourceContext;
    private MockObject $requestStack;
    private MockObject $parametersExtractor;
    private MockObject $resourceFactory;

    #[Test]
    public function itReturnsTheCurrentResource(): void
    {
        $request = new Request(['test']);

        $resource = new Resource(name: 'my_resource');
        $operation = new Show(shortName: 'my_operation');
        $operation = $operation->withResource($resource);
        $resource = $resource->withOperations([$operation]);

        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects($this->atLeastOnce())
            ->method('getResourceName')
            ->with($request)
            ->willReturn('my_resource')
        ;
        $this->resourceFactory
            ->expects(self::once())
            ->method('create')
            ->with('my_resource')
            ->willReturn($resource)
        ;

        $contextResource = $this->resourceContext->getResource();

        $this->assertEquals($resource->getName(), $contextResource->getName());
    }

    #[Test]
    public function itDoesNotReturnAMissingResource(): void
    {
        $request = new Request(['test']);

        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects(self::once())
            ->method('getResourceName')
            ->with($request)
            ->willReturn(null)
        ;

        $this->expectExceptionObject(new Exception('The current request is not supported by any resource'));
        $this->resourceContext->getResource();
    }

    #[Test]
    public function itCheckIfThereIsACurrentResource(): void
    {
        $request = new Request(['test']);
        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects(self::once())
            ->method('getResourceName')
            ->with($request)
            ->willReturn(null)
        ;

        $this->assertFalse($this->resourceContext->hasResource());
    }

    protected function setUp(): void
    {
        $this->requestStack = self::createMock(RequestStack::class);
        $this->parametersExtractor = self::createMock(ParametersExtractorInterface::class);
        $this->resourceFactory = self::createMock(ResourceFactoryInterface::class);
        $this->resourceContext = new ResourceContext(
            $this->requestStack,
            $this->parametersExtractor,
            $this->resourceFactory,
        );
    }
}
