<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Context;

use LAG\AdminBundle\Exception\ResourceNotFoundException;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Context\ResourceContext;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Metadata\Get;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class ResourceContextTest extends TestCase
{
    private ResourceContextInterface $resourceContext;
    private MockObject $parametersExtractor;
    private MockObject $resourceRegistry;

    #[Test]
    public function itSupportsRequest(): void
    {
        $request = new Request(['some_key' => 'some_value']);

        $this->parametersExtractor
            ->expects(self::once())
            ->method('getApplicationName')
            ->with($request)
            ->willReturn('an_application')
        ;
        $this->parametersExtractor
            ->expects(self::once())
            ->method('getResourceName')
            ->with($request)
            ->willReturn('a_resource')
        ;
        $this->parametersExtractor
            ->expects(self::once())
            ->method('getOperationName')
            ->with($request)
            ->willReturn('an_operation')
        ;

        $support = $this->resourceContext->supports($request);

        self::assertTrue($support);
    }

    #[Test]
    public function itReturnsTheCurrentOperation(): void
    {
        $request = new Request(['some_key' => 'some_value']);
        $operation = new Get(name: 'an_operation');
        $resource = new Resource(name: 'a_resource', application: 'an_application', operations: [$operation]);

        $this->parametersExtractor
            ->expects(self::atLeastOnce())
            ->method('getApplicationName')
            ->with($request)
            ->willReturn('an_application')
        ;
        $this->parametersExtractor
            ->expects(self::atLeastOnce())
            ->method('getResourceName')
            ->with($request)
            ->willReturn('a_resource')
        ;
        $this->parametersExtractor
            ->expects(self::atLeastOnce())
            ->method('getOperationName')
            ->with($request)
            ->willReturn('an_operation')
        ;

        $this->resourceRegistry
            ->expects(self::atLeastOnce())
            ->method('get')
            ->with('a_resource', 'an_application')
            ->willReturn($resource)
        ;

        $extractedResource = $this->resourceContext->getResource($request);
        $extractedOperation = $this->resourceContext->getOperation($request);

        self::assertEquals($resource, $extractedResource);
        self::assertEquals($operation->withResource($resource), $extractedOperation);
    }

    #[Test]
    #[DataProvider(methodName: 'notSupportedResources')]
    public function itDoesNotReturnsNotSupportedResource(
        ?string $applicationName,
        ?string $resourceName,
        ?string $operationName,
    ): void {
        $request = new Request(['some_key' => 'some_value']);

        $this->parametersExtractor
            ->expects(self::atLeastOnce())
            ->method('getApplicationName')
            ->with($request)
            ->willReturn($applicationName)
        ;
        $this->parametersExtractor
            ->method('getResourceName')
            ->with($request)
            ->willReturn($resourceName)
        ;
        $this->parametersExtractor
            ->method('getOperationName')
            ->with($request)
            ->willReturn($operationName)
        ;

        self::expectExceptionObject(new ResourceNotFoundException('The current request is not supported by any admin resource'));

        $this->resourceContext->getResource($request);
    }

    public static function notSupportedResources(): iterable
    {
        yield 'no_application' => [null, 'a_resource', 'an_operation'];
        yield 'no_resource' => ['an_application', null, 'an_operation'];
        yield 'no_operation' => ['an_application', 'a_resource', null];
        yield 'nothing' => [null, null, null];
    }

    protected function setUp(): void
    {
        $this->parametersExtractor = self::createMock(ParametersExtractorInterface::class);
        $this->resourceRegistry = self::createMock(ResourceRegistryInterface::class);
        $this->resourceContext = new ResourceContext(
            $this->parametersExtractor,
            $this->resourceRegistry
        );
    }
}
