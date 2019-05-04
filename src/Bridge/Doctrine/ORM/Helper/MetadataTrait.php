<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Helper;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * @deprecated use the MetaDataHelper instead
 */
trait MetadataTrait
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Return the Doctrine metadata of the given class.
     *
     * @param $class
     *
     * @return ClassMetadata|null
     */
    protected function findMetadata($class)
    {
        $metadata = null;

        try {
            // We could not use the hasMetadataFor() method as it is not working if the entity is not loaded. But
            // the getMetadataFor() method could throw an exception if the class is not found
            $metadata = $this->entityManager->getMetadataFactory()->getMetadataFor($class);
        } catch (Exception $exception) {
            // If an exception is raised, nothing to do. Extra data from metadata will be not used.
        }

        return $metadata;
    }
}
