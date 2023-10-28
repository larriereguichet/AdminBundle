<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Locator;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\AdminResource;
use Symfony\Component\HttpKernel\KernelInterface;

use function Symfony\Component\String\u;

class CompositeLocator implements MetadataLocatorInterface
{
    public function __construct(
        private iterable $locators,
        private KernelInterface $kernel,
    ) {
    }

    public function locateCollection(string $resourceDirectory): array
    {
        $resources = [];

        if (str_starts_with($resourceDirectory, '@')) {
            $bundleName = u($resourceDirectory)->before('/')->after('@')->toString();
            $resourceDirectory = u($this->kernel->getBundle($bundleName)->getPath())
                ->ensureEnd('/')
                ->append(u($resourceDirectory)->after('/')->toString())
                ->toString()
            ;
        }

        /** @var MetadataLocatorInterface $locator */
        foreach ($this->locators as $locator) {
            foreach ($locator->locateCollection($resourceDirectory) as $resource) {
                if (!$resource instanceof AdminResource) {
                    throw new Exception(sprintf('The locator "%s" returns an instance of "%s", expected an instance of "%s"', $locator::class, $resource::class, AdminResource::class));
                }
                $resources[] = $resource;
            }
        }

        return $resources;
    }
}
