<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Initializer;

use LAG\AdminBundle\Metadata\Resource;

interface ResourceIdentifiersInitializerInterface
{
    public function initializeResourceIdentifiers(Resource $resource): Resource;
}
