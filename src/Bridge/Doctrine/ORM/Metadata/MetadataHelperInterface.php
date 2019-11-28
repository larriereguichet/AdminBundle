<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

interface MetadataHelperInterface
{
    public function getFields(string $entityClass): array;

    /**
     * Return the Doctrine metadata of the given class.
     *
     * @param $class
     */
    public function findMetadata($class): ?ClassMetadata;
}
