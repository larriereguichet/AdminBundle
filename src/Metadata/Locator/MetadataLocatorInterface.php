<?php

namespace LAG\AdminBundle\Metadata\Locator;

use LAG\AdminBundle\Metadata\AdminResource;

interface MetadataLocatorInterface
{
    /**
     * @param string $resourceDirectory
     *
     * @return iterable<AdminResource>
     */
    public function locateCollection(string $resourceDirectory): iterable;
}
