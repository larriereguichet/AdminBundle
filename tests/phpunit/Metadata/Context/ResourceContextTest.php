<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Context;

use LAG\AdminBundle\Exception\ResourceNotFoundException;
use LAG\AdminBundle\Request\Extractor\ResourceParametersExtractorInterface;
use LAG\AdminBundle\Resource\Context\ResourceContext;
use LAG\AdminBundle\Resource\Metadata\Show;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
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

        $resource = new Resource(name: 'my_resource');
        $operation = new Show(name: 'my_operation');
        $operation = $operation->withResource($resource);
        $resource = $resource->withOperations([$operation]);

        $this
            ->parametersExtractor
            ->expects($this->atLeastOnce())
            ->method('getApplicationName')
            ->with($request)
            ->willReturn('my_application')
        ;
        $this
            ->parametersExtractor
            ->expects($this->atLeastOnce())
            ->method('getResourceName')
            ->with($request)
            ->willReturn('my_resource')
        ;
        $this
            ->parametersExtractor
            ->expects($this->atLeastOnce())
            ->method('getOperationName')
            ->with($request)
            ->willReturn('my_operation')
        ;

        $this
            ->resourceRegistry
            ->expects(self::once())
            ->method('get')
            ->with('my_resource')
            ->willReturn($resource)
        ;

        $contextResource = $this->resourceContext->getResource($request);
        $this->assertEquals($resource->getName(), $contextResource->getName());
    }

    #[DataProvider('supportsProvider')]
    public function testSupports(?string $application, ?string $resource, ?string $operation, bool $expectedSupport): void
    {
        $request = new Request(['test']);
        $this
            ->parametersExtractor
            ->method('getApplicationName')
            ->with($request)
            ->willReturn($application)
        ;
        $this
            ->parametersExtractor
            ->method('getResourceName')
            ->with($request)
            ->willReturn($resource)
        ;
        $this
            ->parametersExtractor
            ->method('getOperationName')
            ->with($request)
            ->willReturn($operation)
        ;

        $support = $this->resourceContext->supports($request);
        $this->assertEquals($expectedSupport, $support);
    }

    public static function supportsProvider(): array
    {
        return [
            ['admin', null, null, false],
            ['my_application', 'resource', null, false],
            ['my_application', null, 'my_operation', false],
            [null, 'my_operation', 'my_operation', false],
            [null, null, 'my_operation', false],
            [null, null, null, false],
            ['my_application', 'my_resource', 'my_operation', true],
            ['_application', 'resource', '_operation', true],
        ];
    }

    public function testGetWithoutSupport(): void
    {
        $request = new Request(['test']);

        $this
            ->parametersExtractor
            ->expects(self::once())
            ->method('getApplicationName')
            ->with($request)
            ->willReturn(null)
        ;

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage('The current request is not supported by any admin resource');
        $this->resourceContext->getOperation($request);
    }

    protected function setUp(): void
    {
        $this->parametersExtractor = self::createMock(ResourceParametersExtractorInterface::class);
        $this->resourceRegistry = self::createMock(ResourceRegistryInterface::class);
        $this->resourceContext = new ResourceContext(
            $this->parametersExtractor,
            $this->resourceRegistry,
        );
    }
}
