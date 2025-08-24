<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Context;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Context\ResourceContext;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
        $request = new Request();
        $expectedResource = new Resource(name: 'my_resource');

        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects(self::once())
            ->method('getResourceName')
            ->with($request)
            ->willReturn('my_resource')
        ;
        $this->resourceFactory
            ->expects(self::once())
            ->method('create')
            ->with('my_resource')
            ->willReturn($expectedResource)
        ;

        $application = $this->resourceContext->getResource();

        self::assertEquals($expectedResource, $application);
    }

    #[Test]
    public function itDoesNotReturnAMissingResource(): void
    {
        $request = new Request();

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
        $this->resourceFactory
            ->expects(self::never())
            ->method('create')
        ;

        self::expectExceptionObject(new Exception('The current request is not supported by any resource'));

        $this->resourceContext->getResource();
    }

    #[Test]
    public function itChecksForTheCurrentResource(): void
    {
        $request = new Request();

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

        self::assertFalse($this->resourceContext->hasResource());
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
