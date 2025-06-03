<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Tests\Application\Entity\Project;

return static function (): iterable {
    yield new Resource(
        name: 'project',
        resourceClass: Project::class,
    );
};
