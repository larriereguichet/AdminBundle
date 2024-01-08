<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Entity;

use LAG\AdminBundle\Metadata\Resource;

#[Resource]
class FakeEntity
{
    public int $id;
}
