<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Registry\ResourceRegistry;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

final class ResourceRegistryTest extends TestCase
{
    private ResourceRegistryInterface $registry;
    private MockObject $resourceFactory;

    #[Test]
    #[DataProvider(methodName: 'resources')]
    public function itReturnsResources(string $resourceName, ?string $applicationName, Resource $expectedResource): void
    {
        $resource = new Resource(name: $resourceName, application: $applicationName);
        $this->resourceFactory
            ->expects(self::once())
            ->method('create')
            ->with($resource)
            ->willReturn($resource)
        ;
        $resource = $this->registry->get($resourceName, $applicationName);

        self::assertEquals($expectedResource, $resource);
    }

    #[Test]
    public function itDoesNotReturnsNonExistingResources(): void
    {
        $this->resourceFactory
            ->expects(self::never())
            ->method('create')
        ;
        self::expectExceptionObject(new Exception('Resource with name "some_unknown_resource" not found in the application "my_application"'));
        $this->registry->get('some_unknown_resource');
    }

    #[Test]
    #[DataProvider(methodName: 'resourcesExists')]
    public function itCheckIfResourceExists(string $resourceName, ?string $applicationName, bool $expected): void
    {
        $result = $this->registry->has($resourceName, $applicationName);

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function itReturnsAllResources(): void
    {
        $this->resourceFactory
            ->expects(self::exactly(3))
            ->method('create')
            ->willReturnCallback(fn (Resource $resource) => $resource)
        ;
        $resources = $this->registry->all();

        self::assertIsIterable($resources);
        $resources = iterator_to_array($resources);
        self::assertCount(3, $resources);
    }

    public static function resources(): iterable
    {
        yield 'resource_by_name_1' => ['my_resource', null, new Resource(name: 'my_resource')];
        yield 'resource_by_name_2' => ['my_other_resource', 'my_application', new Resource(name: 'my_other_resource', application: 'my_application')];
        yield 'resource_by_name_and_default_application' => ['my_other_resource', 'my_other_application', new Resource(name: 'my_other_resource', application: 'my_other_application')];
    }

    public static function resourcesExists(): iterable
    {
        yield 'exists_by_name_1' => ['my_resource', null, true];
        yield 'exists_by_name_2' => ['my_other_resource', null, true];
        yield 'exists_by_name_and_application' => ['my_other_resource', 'my_other_application', true];

        yield 'missing_1' => ['my_wrong_resource', null, false];
        yield 'missing_2' => ['my_wrong_resource', 'my_wrong_application', false];
    }

    protected function setUp(): void
    {
        $resource1 = new Resource(name: 'my_resource');
        $resource2 = new Resource(name: 'my_other_resource', application: 'my_other_application');
        $resource3 = new Resource(name: 'my_other_resource', application: 'my_application');

        $this->resourceFactory = self::createMock(ResourceFactoryInterface::class);
        $this->registry = new ResourceRegistry(
            [$resource1, $resource2, $resource3],
            'my_application',
            $this->resourceFactory,
        );
    }
}
