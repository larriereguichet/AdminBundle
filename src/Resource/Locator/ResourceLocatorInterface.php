<?php

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Metadata\Admin;

interface ResourceLocatorInterface
{
    /**
     * @param string $resourceDirectory
     *
     * @return iterable<Admin>
     */
    public function locate(string $resourceDirectory): iterable;
}
