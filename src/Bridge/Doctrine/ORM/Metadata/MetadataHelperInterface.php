<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

use Doctrine\Persistence\Mapping\ClassMetadata;

interface MetadataHelperInterface
{
    /**
     * Return the Doctrine metadata of the given class.
     *
     * @param $class
     */
    public function findMetadata($class): ?ClassMetadata;
}
