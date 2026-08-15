<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\ResourceLink;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResourceLinkTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $link = new ResourceLink(name: 'author');

        self::assertSame('author', $link->getName());
        self::assertSame('@LAGAdmin/grids/properties/text.html.twig', $link->getTemplate());
        self::assertTrue($link->isSortable());
        self::assertNull($link->getApplication());
        self::assertNull($link->getResource());
        self::assertNull($link->getOperation());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $link = new ResourceLink(name: 'author');

        $new = $link->withApplication('admin');
        self::assertNotSame($link, $new);
        self::assertSame('admin', $new->getApplication());
        self::assertNull($link->getApplication());

        $new = $link->withResource('author');
        self::assertNotSame($link, $new);
        self::assertSame('author', $new->getResource());

        $new = $link->withOperation('show');
        self::assertNotSame($link, $new);
        self::assertSame('show', $new->getOperation());
    }
}
