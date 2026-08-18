<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\TextFilter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class TextFilterTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $filter = new TextFilter(name: 'search');

        self::assertSame('search', $filter->getName());
        self::assertSame('like', $filter->getComparator());
        self::assertSame('and', $filter->getOperator());
        self::assertSame(TextType::class, $filter->getFormType());
        self::assertNull($filter->getProperties());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $filter = new TextFilter(name: 'search');

        $new = $filter->withProperties(['title', 'description']);
        self::assertNotSame($filter, $new);
        self::assertSame(['title', 'description'], $new->getProperties());
        self::assertNull($filter->getProperties());
    }
}
