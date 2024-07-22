<?php

namespace LAG\AdminBundle\Tests\Fixtures;

use LAG\AdminBundle\Resource\Metadata\Resource;

#[Resource]
class FakeResource
{
    #[\LAG\AdminBundle\Resource\Metadata\Text]
    private int $id;

    #[\LAG\AdminBundle\Resource\Metadata\Text]
    private string $name;

    private string $description;
}
