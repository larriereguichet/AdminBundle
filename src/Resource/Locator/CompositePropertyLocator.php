<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Locator;

final readonly class CompositePropertyLocator implements PropertyLocatorInterface
{
    public function __construct(
        private iterable $locators,
    ) {
    }

    public function locateProperties(string $resourceClass): iterable
    {
        foreach ($this->locators as $locator) {
            yield from $locator->locateProperties($resourceClass);
        }
    }
}
