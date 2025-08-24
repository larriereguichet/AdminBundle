<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Entity;

use LAG\AdminBundle\Entity\TimestampableTrait;
use LAG\AdminBundle\Metadata\Resource;

#[Resource]
class FakeEntity
{
    use TimestampableTrait;

    public int $id;
}
