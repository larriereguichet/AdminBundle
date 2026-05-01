<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;

final readonly class ResourceIdentifiersMetadataFactory implements ResourceMetadataFactoryInterface
{
    public function __construct(
        private ResourceMetadataFactoryInterface $metadataFactory,
        private Registry $registry,
    ) {
    }

    public function createMetadata(string $resourceName): ResourceMetadataInterface
    {
        $metadata = $this->metadataFactory->createMetadata($resourceName);

        if ($metadata->getIdentifiers() !== null || $metadata->getResourceClass() === null) {
            return $metadata;
        }
        /** @var EntityManagerInterface|null $manager */
        $manager = $this->registry->getManagerForClass($metadata->getResourceClass());

        if ($manager === null) {
            return $metadata;
        }
        $classMetadata = $manager->getClassMetadata($metadata->getResourceClass());

        return $metadata->withIdentifiers($classMetadata->getIdentifier());
    }
}
