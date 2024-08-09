<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Resolver;

use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Resolver\PhpFileResolver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PhpFileResolverTest extends TestCase
{
    private PhpFileResolver $resolver;

    #[Test]
    public function itLoadGridFromAPhpFile(): void
    {
        $result = $this->resolver->resolveFile(__DIR__.'/../../../fixtures/grids/test_grid.php');

        self::assertIsIterable($result);

        $result = iterator_to_array($result);
        self::assertCount(1, $result);

        foreach ($result as $grid) {
            self::assertInstanceOf(Grid::class, $grid);
        }
    }

    protected function setUp(): void
    {
        $this->resolver = new PhpFileResolver();
    }
}
