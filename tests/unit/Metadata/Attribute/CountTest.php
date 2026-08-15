<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Grid\DataTransformer\CountDataTransformer;
use LAG\AdminBundle\Metadata\Attribute\Count;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CountTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $count = new Count(name: 'reviewsCount');

        self::assertSame('reviewsCount', $count->getName());
        self::assertSame('@LAGAdmin/grids/properties/count.html.twig', $count->getTemplate());
        self::assertTrue($count->isSortable());
        self::assertSame(CountDataTransformer::class, $count->getDataTransformer());
    }
}
