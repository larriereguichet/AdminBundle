<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Boolean;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BooleanTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $boolean = new Boolean(name: 'active');

        self::assertSame('active', $boolean->getName());
        self::assertSame('@LAGAdmin/grids/properties/boolean.html.twig', $boolean->getTemplate());
        self::assertTrue($boolean->isSortable());
        self::assertTrue($boolean->isTranslatable());
    }
}
