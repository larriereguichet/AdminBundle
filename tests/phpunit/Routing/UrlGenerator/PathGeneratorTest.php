<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Routing\UrlGenerator;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\GetCollection;
use LAG\AdminBundle\Routing\UrlGenerator\PathGenerator;
use LAG\AdminBundle\Tests\TestCase;

class PathGeneratorTest extends TestCase
{
    private PathGenerator $generator;

    /** @dataProvider pathProvider */
    public function testGeneratePath(
        string $resourceName,
        string $resourcePrefix,
        ?string $operationPath,
        string $expectedPath,
        ?array $routeParameters = null,
    ): void {
        $resource = (new AdminResource())
            ->withName($resourceName)
            ->withRoutePrefix($resourcePrefix)
        ;
        $operation = (new GetCollection())
            ->withResource($resource)
            ->withPath($operationPath)
            ->withRouteParameters($routeParameters)
        ;

        $path = $this->generator->generatePath($operation);

        $this->assertEquals($expectedPath, $path);
    }

    public static function pathProvider(): array
    {
        return [
            ['category', '/cms/{resourceName}', '/get', '/cms/categories/get'],
            ['articles', '/prefix/{resourceName}', '/list', '/prefix/articles/list'],
            ['articles', '/{resourceName}', '/list', '/articles/list'],
            // ['articles', '/{resourceName}', null, '/articles'],
            ['articles', '/{resourceName}', null, '/articles/{id}/{slug}', ['id' => null, 'slug' => null]],
            ['articles', '/{resourceName}', '/test/', '/articles/test', ['id' => null, 'slug' => null]],
        ];
    }

    protected function setUp(): void
    {
        $this->generator = new PathGenerator();
    }
}
