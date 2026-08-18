<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Map;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MapTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $map = new Map(name: 'status', map: ['active' => 'Active', 'inactive' => 'Inactive']);

        self::assertSame('status', $map->getName());
        self::assertSame('@LAGAdmin/grids/properties/map.html.twig', $map->getTemplate());
        self::assertSame(['active' => 'Active', 'inactive' => 'Inactive'], $map->getMap());
    }

    #[Test]
    public function itReturnsImmutableCopyForWithMap(): void
    {
        $map = new Map(name: 'status', map: ['active' => 'Active']);

        $new = $map->withMap(['enabled' => 'Enabled', 'disabled' => 'Disabled']);
        self::assertNotSame($map, $new);
        self::assertSame(['enabled' => 'Enabled', 'disabled' => 'Disabled'], $new->getMap());
        self::assertSame(['active' => 'Active'], $map->getMap());
    }
}
