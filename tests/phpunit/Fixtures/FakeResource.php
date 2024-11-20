<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Fixtures;

use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Text;

#[Resource(application: 'shop')]
#[Resource(application: 'admin')]
class FakeResource
{
    #[Text]
    private readonly int $id;

    #[Text]
    private readonly string $name;

    private readonly string $description;
}
