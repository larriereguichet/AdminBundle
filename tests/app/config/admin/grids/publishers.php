<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Config\LAGAdminBuilder;
use LAG\AdminBundle\Metadata\Attribute\Grid;

return static function (LAGAdminBuilder $builder): void {
    $builder->addGrid('publishers', new Grid(
        properties: ['id', 'name'],
    ));
};
