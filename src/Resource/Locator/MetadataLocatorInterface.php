<?php

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Resource\Metadata\Resource;

interface MetadataLocatorInterface
{
    public function locateMetadata(Resource $resource): void;
}
