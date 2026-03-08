<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\DependencyInjection\Locator\ClassLocator;
use LAG\AdminBundle\Exception\Resource\MissingResourceNameException;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;

use function Symfony\Component\String\u;

final readonly class ResourceCollectionAttributeMetadataFactory implements ResourceCollectionMetadataFactoryInterface
{
    /** @param array<string> $paths */
    public function __construct(
        private ResourceCollectionMetadataFactoryInterface $metadataFactory,
        private array $paths,
    ) {
    }

    public function createMetadata(): array
    {
        $resources = $this->metadataFactory->createMetadata();
        $locator = new ClassLocator();

        foreach ($locator->locateClassesByPaths($this->paths) as $resourceClass) {
            $reflectionClass = new \ReflectionClass($resourceClass);
            $attributes = $reflectionClass->getAttributes(Resource::class, \ReflectionAttribute::IS_INSTANCEOF);

            foreach ($attributes as $attribute) {
                /** @var ResourceMetadataInterface $resource */
                $resource = $attribute->newInstance();

                if ($resource->getShortName() === null) {
                    $resource = $resource->withShortName(
                        u($reflectionClass->getShortName())
                            ->snake()
                            ->lower()
                            ->toString()
                    );
                }

                if (!$resource->getResourceClass()) {
                    $resource = $resource->withResourceClass($reflectionClass->getName());
                }

                if (!$resource->getShortName()) {
                    throw new MissingResourceNameException($resourceClass);
                }
                $resources[$resource->getApplicationName().'.'.$resource->getShortName()] = $resource;
            }
        }

        return $resources;
    }
}
