<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GroupTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $group = new Group(name: 'details');

        self::assertSame('details', $group->getName());
        self::assertSame('@LAGAdmin/grids/properties/group.html.twig', $group->getTemplate());
        self::assertFalse($group->isSortable());
        self::assertSame([], $group->getProperties());
    }

    #[Test]
    public function itReturnsImmutableCopyForWithProperties(): void
    {
        $group = new Group(name: 'details');

        $new = $group->withProperties(['firstName', 'lastName']);
        self::assertNotSame($group, $new);
        self::assertSame(['firstName', 'lastName'], $new->getProperties());
        self::assertSame([], $group->getProperties());
    }
}
