<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\PropertyGuesser;

use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

interface PropertyGuesserInterface
{
    /**
     * Return a property according to the given php type or class. If no property is found, null is returned.
     */
    public function guessProperty(string $dataClass, string $propertyName, ?string $propertyType): ?PropertyInterface;
}
