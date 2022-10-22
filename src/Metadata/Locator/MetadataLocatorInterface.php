<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Locator;

use LAG\AdminBundle\Metadata\AdminResource;

interface MetadataLocatorInterface
{
    /**
     * @return iterable<AdminResource>
     */
    public function locateCollection(string $resourceDirectory): iterable;
}
