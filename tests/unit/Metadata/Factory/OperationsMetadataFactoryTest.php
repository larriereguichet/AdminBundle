<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Application;
use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\Factory\ApplicationMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\OperationsMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OperationsMetadataFactoryTest extends TestCase
{
    #[Test]
    public function itGeneratesTitleAndPathForCollectionOperation(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index()],
        );
        $application = new Application(name: 'admin', baseTemplate: '@LAGAdmin/base.html.twig');

        $factory = $this->createFactory($resource, $application, 'admin.book.index');
        $result = $factory->createMetadata('book');

        $operations = array_values($result->getOperations());
        self::assertCount(1, $operations);

        $operation = $operations[0];
        self::assertSame('index', $operation->getShortName());
        self::assertSame('Books', $operation->getTitle());
        self::assertSame('/books/index', $operation->getPath());
        self::assertSame('admin.book.index', $operation->getRoute());
        self::assertSame('@LAGAdmin/base.html.twig', $operation->getBaseTemplate());
    }

    #[Test]
    public function itGeneratesTitleAndPathForNonCollectionOperation(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(), new Show()],
        );
        $application = new Application(name: 'admin');

        $factory = $this->createFactory($resource, $application, 'admin.book.index');
        $result = $factory->createMetadata('book');

        $operations = $result->getOperations();
        $showOp = null;

        foreach ($operations as $op) {
            if ($op->getShortName() === 'show') {
                $showOp = $op;
                break;
            }
        }

        self::assertNotNull($showOp);
        self::assertSame('Show book', $showOp->getTitle());
        self::assertSame('/books/{id}/show', $showOp->getPath());
    }

    #[Test]
    public function itAddsTheOperationIdentifiersToTheGeneratedPath(): void
    {
        $show = new Show(identifiers: ['slug']);
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [$show],
            identifiers: ['id'],
        );
        $application = new Application(name: 'admin');

        $factory = $this->createFactory($resource, $application, 'admin.book.show');
        $result = $factory->createMetadata('book');

        $showOp = current($result->getOperations());
        self::assertSame('/books/{slug}/show', $showOp->getPath());
        self::assertSame(['slug'], $showOp->getIdentifiers());
    }

    #[Test]
    public function itAddsEachResourceIdentifierToTheGeneratedPath(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Show()],
            identifiers: ['author', 'id'],
        );
        $application = new Application(name: 'admin');

        $factory = $this->createFactory($resource, $application, 'admin.book.show');
        $result = $factory->createMetadata('book');

        $showOp = current($result->getOperations());
        self::assertSame('/books/{author}/{id}/show', $showOp->getPath());
    }

    #[Test]
    public function itKeepsAnExplicitlyEmptyIdentifiersList(): void
    {
        // The "my account" / "shop configuration" pattern: an item operation addressing an implicit record that the
        // provider resolves from the user context, so it carries no identifier in its path on purpose. No explicit
        // path here, as that is the only case exercising the identifier generation
        $update = new Update(name: 'account', identifiers: []);
        $resource = new Resource(
            shortName: 'user',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [$update],
            identifiers: ['id'],
        );
        $application = new Application(name: 'admin');

        $factory = $this->createFactory($resource, $application, 'admin.user.account');
        $result = $factory->createMetadata('user');

        $operation = current($result->getOperations());
        self::assertSame([], $operation->getIdentifiers());
        self::assertSame('/users/account', $operation->getPath());
        self::assertStringNotContainsString('{', $operation->getPath());
    }

    #[Test]
    public function itDoesNotAddIdentifiersToTheCreateOperationPath(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Create()],
            identifiers: ['id'],
        );
        $application = new Application(name: 'admin');

        $factory = $this->createFactory($resource, $application, 'admin.book.create');
        $result = $factory->createMetadata('book');

        $createOp = current($result->getOperations());
        self::assertSame('/books/create', $createOp->getPath());
        self::assertSame([], $createOp->getIdentifiers());
    }

    #[Test]
    public function itUsesExistingPathWhenOperationHasOne(): void
    {
        $show = new Show(path: '/custom/show');
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(), $show],
        );
        $application = new Application(name: 'admin');

        $factory = $this->createFactory($resource, $application, 'admin.book.index');
        $result = $factory->createMetadata('book');

        foreach ($result->getOperations() as $op) {
            if ($op->getShortName() === 'show') {
                self::assertSame('/custom/show', $op->getPath());

                return;
            }
        }

        self::fail('Show operation not found');
    }

    #[Test]
    public function itPrependsPathPrefixToExistingPath(): void
    {
        $show = new Show(path: '/show');
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(), $show],
            pathPrefix: '/admin',
        );
        $application = new Application(name: 'admin');

        $factory = $this->createFactory($resource, $application, 'admin.book.index');
        $result = $factory->createMetadata('book');

        foreach ($result->getOperations() as $op) {
            if ($op->getShortName() === 'show') {
                self::assertSame('/admin/show', $op->getPath());

                return;
            }
        }

        self::fail('Show operation not found');
    }

    #[Test]
    public function itSetsRouteParametersFromIdentifiers(): void
    {
        $show = new Show(identifiers: ['id']);
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(), $show],
        );
        $application = new Application(name: 'admin');

        $factory = $this->createFactory($resource, $application, 'admin.book.index');
        $result = $factory->createMetadata('book');

        foreach ($result->getOperations() as $op) {
            if ($op->getShortName() === 'show') {
                self::assertSame(['id' => 'id'], $op->getRouteParameters());

                return;
            }
        }

        self::fail('Show operation not found');
    }

    #[Test]
    public function itExpandsShortRedirectOperation(): void
    {
        $show = new Show(redirectOperation: 'index');
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [$show],
        );
        $application = new Application(name: 'admin');

        $factory = $this->createFactory($resource, $application, 'admin.book.show');
        $result = $factory->createMetadata('book');

        $showOp = current($result->getOperations());
        self::assertSame('index', $showOp->getRedirectOperation());
    }

    #[Test]
    public function itReturnsEmptyRouteParametersWhenPathHasNoBraces(): void
    {
        $show = new Show(identifiers: ['id'], path: '/books/show');
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(), $show],
        );
        $application = new Application(name: 'admin');

        $factory = $this->createFactory($resource, $application, 'admin.book.index');
        $result = $factory->createMetadata('book');

        foreach ($result->getOperations() as $op) {
            if ($op->getShortName() === 'show') {
                self::assertSame([], $op->getRouteParameters());

                return;
            }
        }

        self::fail('Show operation not found');
    }

    #[Test]
    public function itMergesResourceContextIntoOperationContext(): void
    {
        $index = new Index(context: ['operation_key' => 'operation_value', 'shared_key' => 'operation_override']);
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [$index],
            context: ['resource_key' => 'resource_value', 'shared_key' => 'resource_default'],
        );
        $application = new Application(name: 'admin', baseTemplate: '@LAGAdmin/base.html.twig');

        $factory = $this->createFactory($resource, $application, 'admin.book.index');
        $result = $factory->createMetadata('book');

        $operation = current($result->getOperations());
        self::assertSame([
            'resource_key' => 'resource_value',
            'shared_key' => 'operation_override',
            'operation_key' => 'operation_value',
        ], $operation->getContext());
    }

    private function createFactory(
        Resource $resource,
        Application $application,
        string $route,
    ): OperationsMetadataFactory {
        $resourceFactory = $this->createStub(ResourceMetadataFactoryInterface::class);
        $resourceFactory->method('createMetadata')->willReturn($resource);

        $applicationFactory = $this->createStub(ApplicationMetadataFactoryInterface::class);
        $applicationFactory->method('createMetadata')->willReturn($application);

        $routeNameGenerator = $this->createStub(RouteNameGeneratorInterface::class);
        $routeNameGenerator->method('generateRouteName')->willReturn($route);

        return new OperationsMetadataFactory($resourceFactory, $applicationFactory, $routeNameGenerator);
    }
}
