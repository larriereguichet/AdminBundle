<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\ClassMetadata;

final readonly class MetadataHelper implements MetadataHelperInterface
{
    private Collection $cache;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->cache = new ArrayCollection();
    }

    public function findMetadata(string $class): ?ClassMetadata
    {
        if (!$this->cache->containsKey($class)) {
            try {
                // We could not use the hasMetadataFor() method as it is not working if the entity is not loaded. But
                // the getMetadataFor() method could throw an exception if the class is not found
                $this->cache->set($class, $this->entityManager->getMetadataFactory()->getMetadataFor($class));
            } catch (\Exception) {
                // If an exception is raised, nothing to do. Extra data from metadata will be not used.
                return null;
            }
        }

        return $this->cache->get($class);
    }
}
