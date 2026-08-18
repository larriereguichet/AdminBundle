<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\RichText;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RichTextTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $richText = new RichText(name: 'content');

        self::assertSame('content', $richText->getName());
        self::assertSame('@LAGAdmin/grids/properties/rich_text.html.twig', $richText->getTemplate());
        self::assertFalse($richText->isSortable());
        self::assertSame(100, $richText->getLength());
        self::assertSame('...', $richText->getReplace());
        self::assertSame('~', $richText->getEmpty());
        self::assertSame('', $richText->getSuffix());
        self::assertSame('', $richText->getPrefix());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $richText = new RichText(name: 'content');

        $new = $richText->withLength(200);
        self::assertNotSame($richText, $new);
        self::assertSame(200, $new->getLength());

        $new = $richText->withReplace('…');
        self::assertNotSame($richText, $new);
        self::assertSame('…', $new->getReplace());

        $new = $richText->withEmpty('-');
        self::assertNotSame($richText, $new);
        self::assertSame('-', $new->getEmpty());

        $new = $richText->setSuffix(' (read more)');
        self::assertNotSame($richText, $new);
        self::assertSame(' (read more)', $new->getSuffix());

        $new = $richText->setPrefix('> ');
        self::assertNotSame($richText, $new);
        self::assertSame('> ', $new->getPrefix());
    }
}
