<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Resource;

use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Metadata\Boolean;
use LAG\AdminBundle\Metadata\Date;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Metadata\RichText;
use LAG\AdminBundle\Metadata\Text;
use LAG\AdminBundle\Resource\PropertyGuesser\PropertyGuesserInterface;

final readonly class ORMPropertyGuesser implements PropertyGuesserInterface
{
    public function __construct(
        private PropertyGuesserInterface $propertyGuesser,
        private MetadataHelperInterface $metadataHelper,
    ) {
    }

    public function guessProperty(string $dataClass, string $propertyName, ?string $propertyType): ?PropertyInterface
    {
        $metadata = $this->metadataHelper->findMetadata($dataClass);

        if ($metadata === null || !$metadata->hasField($propertyName)) {
            return $this->propertyGuesser->guessProperty($dataClass, $propertyName, $propertyType);
        }
        $fieldType = $metadata->getTypeOfField($propertyName);

        return match ($fieldType) {
            'string' => new Text(name: $propertyName),
            'text' => new RichText(name: $propertyName),
            'boolean' => new Boolean(name: $propertyName),
            'date', 'datetime', 'date_immutable', 'datetime_immutable' => new Date(name: $propertyName),
            default => $this->propertyGuesser->guessProperty($dataClass, $propertyName, $propertyType),
        };
    }
}
