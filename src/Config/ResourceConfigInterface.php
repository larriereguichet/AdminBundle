<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config;

use LAG\AdminBundle\Metadata\Resource;
use Symfony\Component\Config\Builder\ConfigBuilderInterface;

interface ResourceConfigInterface extends ConfigBuilderInterface
{
    public function addResource(Resource $resource): self;
}
