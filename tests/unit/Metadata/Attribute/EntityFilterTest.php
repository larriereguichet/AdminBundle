<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\EntityFilter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class EntityFilterTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $filter = new EntityFilter(name: 'category');

        self::assertSame('category', $filter->getName());
        self::assertSame('=', $filter->getComparator());
        self::assertSame('and', $filter->getOperator());
        self::assertSame(EntityType::class, $filter->getFormType());
        self::assertNull($filter->getProperty());
        self::assertFalse($filter->isMultiple());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $filter = new EntityFilter(name: 'category');

        $new = $filter->withProperty('category.id');
        self::assertNotSame($filter, $new);
        self::assertSame('category.id', $new->getProperty());
        self::assertNull($filter->getProperty());

        $new = $filter->withMultiple(true);
        self::assertNotSame($filter, $new);
        self::assertTrue($new->isMultiple());
        self::assertFalse($filter->isMultiple());
    }
}
