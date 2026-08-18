<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Collection;
use LAG\AdminBundle\Metadata\Attribute\Text;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CollectionPropertyTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $collection = new Collection(name: 'tags');

        self::assertSame('tags', $collection->getName());
        self::assertSame('@LAGAdmin/grids/properties/collection.html.twig', $collection->getTemplate());
        self::assertFalse($collection->isSortable());
        self::assertNull($collection->getEntryProperty());
    }

    #[Test]
    public function itReturnsImmutableCopyForWithEntryProperty(): void
    {
        $collection = new Collection(name: 'tags');
        $entryProperty = new Text(name: 'name');

        $new = $collection->withEntryProperty($entryProperty);
        self::assertNotSame($collection, $new);
        self::assertSame($entryProperty, $new->getEntryProperty());
        self::assertNull($collection->getEntryProperty());
    }
}
