<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Exception;

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
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata|null
     */
    protected function findMetadata($class)
    {
        $metadata = null;

        try {
            // We could not use the hasMetadataFor() method as it is not working if the entity is not loaded. But
            // the getMetadataFor() method could throw an exception if the class is not found
            $metadata = $this->entityManager->getMetadataFactory()->getMetadataFor($class);
        } catch (Exception $exception) {}

        return $metadata;
    }
}
