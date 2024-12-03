<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

return static function (): iterable {
    yield new Grid(
        name: 'projects_table',
        properties: ['id'],
    );
};
