<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\DependencyInjection\Locator;

use LAG\AdminBundle\DependencyInjection\Locator\ClassLocator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ClassLocatorTest extends TestCase
{
    private ClassLocator $locator;

    #[Test]
    public function itLocatesClassesInPath(): void
    {
        $classes = iterator_to_array($this->locator->locateClasses(__DIR__.'/../../Fixtures'));

        self::assertContains(\LAG\AdminBundle\Tests\Unit\Fixtures\Book::class, $classes);
        self::assertContains(\LAG\AdminBundle\Tests\Unit\Fixtures\Author::class, $classes);
        self::assertNotContains('LAG\AdminBundle\Tests\Unit\Fixtures\no_class', $classes);
    }

    #[Test]
    public function itLocatesClassesByMultiplePaths(): void
    {
        $paths = [__DIR__.'/../../Fixtures'];
        $classes = iterator_to_array($this->locator->locateClassesByPaths($paths));

        self::assertContains(\LAG\AdminBundle\Tests\Unit\Fixtures\Book::class, $classes);
    }

    protected function setUp(): void
    {
        $this->locator = new ClassLocator();
    }
}
