<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Entity;

use LAG\AdminBundle\Entity\TimestampResourceTrait;
use LAG\AdminBundle\Metadata\Attribute\Resource;

#[Resource]
class Book
{
    use TimestampResourceTrait;

    public int $id;
}
