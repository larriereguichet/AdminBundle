<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Date;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DateTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $date = new Date(name: 'createdAt');

        self::assertSame('createdAt', $date->getName());
        self::assertSame('@LAGAdmin/grids/properties/date.html.twig', $date->getTemplate());
        self::assertSame('medium', $date->getDateFormat());
        self::assertSame('none', $date->getTimeFormat());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $date = new Date(name: 'createdAt');

        $new = $date->withDateFormat('short');
        self::assertNotSame($date, $new);
        self::assertSame('short', $new->getDateFormat());
        self::assertSame('medium', $date->getDateFormat());

        $new = $date->withTimeFormat('short');
        self::assertNotSame($date, $new);
        self::assertSame('short', $new->getTimeFormat());
        self::assertSame('none', $date->getTimeFormat());
    }
}
