<?php

namespace LAG\AdminBundle\Tests\Config;

use LAG\AdminBundle\Config\GridConfig;
use LAG\AdminBundle\Metadata\Grid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GridConfigTest extends TestCase
{
    #[Test]
    public function itConvertsAResourceToAnArray(): void
    {
        $grid = new Grid(name: 'my_grid');

        $config = new GridConfig();
        $config->addGrid($grid);

        $data = $config->toArray();

        self::assertEquals('my_grid', $data['grids'][0]['name']);
        self::assertEquals('lag_admin', $config->getExtensionAlias());
    }
}
