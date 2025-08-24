<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Routing\UrlGenerator;

use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Routing\UrlGenerator\PathGenerator;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class PathGeneratorTest extends TestCase
{
    private PathGenerator $generator;

    #[Test]
    public function itGeneratesPath(): void
    {
        $resource = new Resource(name: 'my_resource');
        $operation = (new Index())
            ->setResource($resource)
            ->withPath('/some-path')
        ;

        $path = $this->generator->generatePath($operation);

        $this->assertEquals('/some-path', $path);
    }

    protected function setUp(): void
    {
        $this->generator = new PathGenerator();
    }
}
