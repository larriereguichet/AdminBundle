<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Config\Source;

use LAG\AdminBundle\Config\Source\RootSnakeCaseSource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RootSnakeCaseSourceTest extends TestCase
{
    #[Test]
    public function itConvertsRootsKeys(): void
    {
        $source = RootSnakeCaseSource::array([
            'id' => 1,
            'my_name' => 'My name',
            'some_options' => ['an_option' => 'value'],
            'some_existing' => true,
            'someExisting' => false,
            'not_string',
        ]);

        self::assertEquals([
            'id' => 1,
            'myName' => 'My name',
            'someOptions' => ['an_option' => 'value'],
            'someExisting' => false,
            'not_string',
        ], iterator_to_array($source->getIterator()));
    }
}
