<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Attribute\Resource;

interface ResourceInitializerInterface
{
    public function initializeResource(Resource $resource): Resource;
}
