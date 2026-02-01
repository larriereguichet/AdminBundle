<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Routing\UrlGenerator;

use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Routing\UrlGenerator\PathGenerator;
use LAG\AdminBundle\Tests\Unit\TestCase;
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
