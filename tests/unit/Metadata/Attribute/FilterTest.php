<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Filter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class FilterTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $filter = new Filter(name: 'search');

        self::assertSame('search', $filter->getName());
        self::assertSame('=', $filter->getComparator());
        self::assertSame('and', $filter->getOperator());
        self::assertSame(TextType::class, $filter->getFormType());
        self::assertSame([], $filter->getFormOptions());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $filter = new Filter(name: 'search');

        $new = $filter->withName('title');
        self::assertNotSame($filter, $new);
        self::assertSame('title', $new->getName());
        self::assertSame('search', $filter->getName());

        $new = $filter->withComparator('like');
        self::assertNotSame($filter, $new);
        self::assertSame('like', $new->getComparator());

        $new = $filter->withOperator('or');
        self::assertNotSame($filter, $new);
        self::assertSame('or', $new->getOperator());

        $new = $filter->withFormType(\Symfony\Component\Form\Extension\Core\Type\IntegerType::class);
        self::assertNotSame($filter, $new);
        self::assertSame(\Symfony\Component\Form\Extension\Core\Type\IntegerType::class, $new->getFormType());

        $new = $filter->withFormOptions(['attr' => ['placeholder' => 'Search...']]);
        self::assertNotSame($filter, $new);
        self::assertSame(['attr' => ['placeholder' => 'Search...']], $new->getFormOptions());
    }
}
