<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

use LAG\AdminBundle\Config\ResourceConfig;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Tests\Application\Entity\Publisher;

return static function (ResourceConfig $resourceConfig): void {
    $resourceConfig->addResource('publisher', new Resource(
        resourceClass: Publisher::class,
    ));
};
