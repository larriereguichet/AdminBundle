<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Entity;

use LAG\AdminBundle\Metadata\AdminResource;

#[AdminResource]
class FakeEntity
{
    public int $id;
}
