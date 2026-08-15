<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Text;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TextTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $text = new Text(name: 'title');

        self::assertSame('title', $text->getName());
        self::assertSame('@LAGAdmin/grids/properties/text.html.twig', $text->getTemplate());
        self::assertTrue($text->isSortable());
        self::assertSame(100, $text->getLength());
        self::assertSame('...', $text->getReplace());
        self::assertSame('~', $text->getEmpty());
        self::assertSame('', $text->getSuffix());
        self::assertSame('', $text->getPrefix());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $text = new Text(name: 'title');

        $new = $text->withLength(50);
        self::assertNotSame($text, $new);
        self::assertSame(50, $new->getLength());
        self::assertSame(100, $text->getLength());

        $new = $text->withReplace('…');
        self::assertNotSame($text, $new);
        self::assertSame('…', $new->getReplace());

        $new = $text->withEmpty('-');
        self::assertNotSame($text, $new);
        self::assertSame('-', $new->getEmpty());

        $new = $text->setSuffix(' EUR');
        self::assertNotSame($text, $new);
        self::assertSame(' EUR', $new->getSuffix());

        $new = $text->setPrefix('$ ');
        self::assertNotSame($text, $new);
        self::assertSame('$ ', $new->getPrefix());
    }
}
