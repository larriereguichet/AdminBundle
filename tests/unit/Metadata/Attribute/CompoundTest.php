<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Compound;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CompoundTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $compound = new Compound(name: 'fullName');

        self::assertSame('fullName', $compound->getName());
        self::assertFalse($compound->isSortable());
        self::assertSame([], $compound->getProperties());
    }

    #[Test]
    public function itReturnsImmutableCopyForWithProperties(): void
    {
        $compound = new Compound(name: 'fullName');

        $new = $compound->withProperties(['firstName', 'lastName']);
        self::assertNotSame($compound, $new);
        self::assertSame(['firstName', 'lastName'], $new->getProperties());
        self::assertSame([], $compound->getProperties());
    }
}
