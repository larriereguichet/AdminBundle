<?php

namespace LAG\AdminBundle\Tests\Fixtures;

use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Property as Admin;

#[Resource]
class FakeResource
{
    #[Admin\Text]
    private int $id;

    #[Admin\Text]
    private string $name;

    private string $description;
}
