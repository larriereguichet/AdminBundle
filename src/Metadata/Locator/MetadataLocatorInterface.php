<?php

namespace LAG\AdminBundle\Metadata\Locator;

use LAG\AdminBundle\Metadata\Admin;

interface MetadataLocatorInterface
{
    /**
     * @param string $resourceDirectory
     *
     * @return iterable<Admin>
     */
    public function locateCollection(string $resourceDirectory): iterable;
}
