<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Metadata\Attribute\Grid;

return static function (): void {
    new Grid(
        name: 'projects_table',
        properties: ['id'],
    );
};
