<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Tests\Application\Entity\Project;

return static function (): iterable {
    yield new Resource(
        name: 'project',
        dataClass: Project::class,
    );
};
