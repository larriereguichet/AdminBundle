<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Title;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TitleTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $title = new Title(name: 'title');

        self::assertSame('title', $title->getName());
        self::assertSame('@LAGAdmin/grids/properties/title.html.twig', $title->getTemplate());
        self::assertFalse($title->isSortable());
        self::assertSame(100, $title->getLength());
        self::assertSame('...', $title->getReplace());
        self::assertSame('~', $title->getEmpty());
        self::assertSame('', $title->getSuffix());
        self::assertSame('', $title->getPrefix());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $title = new Title(name: 'title');

        $new = $title->withLength(50);
        self::assertNotSame($title, $new);
        self::assertSame(50, $new->getLength());

        $new = $title->withReplace('…');
        self::assertNotSame($title, $new);
        self::assertSame('…', $new->getReplace());

        $new = $title->withEmpty('-');
        self::assertNotSame($title, $new);
        self::assertSame('-', $new->getEmpty());

        $new = $title->setSuffix(' (new)');
        self::assertNotSame($title, $new);
        self::assertSame(' (new)', $new->getSuffix());

        $new = $title->setPrefix('[');
        self::assertNotSame($title, $new);
        self::assertSame('[', $new->getPrefix());
    }
}
