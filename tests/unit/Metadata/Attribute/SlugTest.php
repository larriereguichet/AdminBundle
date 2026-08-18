<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Slug;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SlugTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $slug = new Slug(name: 'slug');

        self::assertSame('slug', $slug->getName());
        self::assertSame('@LAGAdmin/grids/properties/slug.html.twig', $slug->getTemplate());
        self::assertSame(['name'], $slug->getSource());
        self::assertSame('default', $slug->getSlugger());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $slug = new Slug(name: 'slug');

        $new = $slug->withSource('title');
        self::assertNotSame($slug, $new);
        self::assertSame(['title'], $new->getSource());
        self::assertSame(['name'], $slug->getSource());

        $new = $slug->withSlugger('ascii');
        self::assertNotSame($slug, $new);
        self::assertSame('ascii', $new->getSlugger());
        self::assertSame('default', $slug->getSlugger());
    }
}
