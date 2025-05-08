<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\PropertyGuesser;

use LAG\AdminBundle\Metadata\Date;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Metadata\Text;

final readonly class PropertyGuesser implements PropertyGuesserInterface
{
    public function guessProperty(string $dataClass, string $propertyName, ?string $propertyType): ?PropertyInterface
    {
        return match ($propertyType) {
            'string', 'integer', 'float' => new Text(name: $propertyName),
            \DateTime::class, \DateTimeImmutable::class => new Date(name: $propertyName),
            default => null,
        };
    }
}
