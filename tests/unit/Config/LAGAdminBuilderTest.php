<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Config;

use LAG\AdminBundle\Config\LAGAdminBuilder;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LAGAdminBuilderTest extends TestCase
{
    #[Test]
    public function itAddsAndReturnsResources(): void
    {
        $builder = new LAGAdminBuilder('test');
        $builder->addResource('publisher', new Resource());

        $resources = $builder->getResources();

        self::assertArrayHasKey('admin.publisher', $resources);
        self::assertSame('publisher', $resources['admin.publisher']->getShortName());
    }

    #[Test]
    public function itAddsAndReturnsGrids(): void
    {
        $builder = new LAGAdminBuilder('test');
        $builder->addGrid('publisher_list', new Grid());

        $grids = $builder->getGrids();

        self::assertArrayHasKey('publisher_list', $grids);
        self::assertSame('publisher_list', $grids['publisher_list']->getName());
    }

    #[Test]
    public function itExposesEnvironment(): void
    {
        $builder = new LAGAdminBuilder('prod');

        self::assertSame('prod', $builder->env());
    }
}
