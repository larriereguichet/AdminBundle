<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Exception;

class MetadataHelper implements MetadataHelperInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findMetadata($class): ?ClassMetadata
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
