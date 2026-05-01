<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit;

use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Delete;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\Update;

trait DataProviderTestTrait
{
    public static function operations(): iterable
    {
        yield [new Index()];
        yield [new Show()];
        yield [new Create()];
        yield [new Update()];
        yield [new Delete()];
    }
}
