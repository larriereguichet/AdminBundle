<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Routing\UrlGenerator;

use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Routing\UrlGenerator\PathGenerator;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class PathGeneratorTest extends TestCase
{
    private PathGenerator $generator;

    #[Test]
    #[DataProvider(methodName: 'pathProvider')]
    public function testGeneratePath(
        string $resourceName,
        string $resourcePrefix,
        ?string $operationPath,
        string $expectedPath,
        array $routeParameters = null,
    ): void {
        $resource = (new Resource())
            ->withName($resourceName)
            ->withPathPrefix($resourcePrefix)
        ;
        $operation = (new Index())
            ->withResource($resource)
            ->withPath($operationPath)
            ->withRouteParameters($routeParameters)
        ;

        $path = $this->generator->generatePath($operation);

        $this->assertEquals($expectedPath, $path);
    }

    public static function pathProvider(): iterable
    {
        yield ['category', '/cms/categories', '/get', '/cms/categories/get'];
        yield ['articles', '/prefix/articles', '/list', '/prefix/articles/list'];
        yield ['articles', '/articles', '/', '/articles'];
        yield ['articles', 'articles', '/', '/articles'];
        yield ['articles', 'articles', '', '/articles'];
        yield ['articles', 'articles', null, '/articles'];
        yield ['articles', '/articles', null, '/articles/{id}/{slug}', ['id' => null, 'slug' => null]];
        yield ['articles', '/articles', '/{slug}/an-operation', '/articles/{slug}/an-operation', ['slug' => null]];
    }

    protected function setUp(): void
    {
        $this->generator = new PathGenerator();
    }
}
