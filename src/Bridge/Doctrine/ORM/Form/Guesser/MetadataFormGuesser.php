<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Form\Guesser;

use Doctrine\ORM\Mapping\GeneratedValue;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Form\Guesser\FormGuesserInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

final readonly class MetadataFormGuesser implements FormGuesserInterface
{
    public function __construct(
        private FormGuesserInterface $formGuesser,
        private MetadataHelperInterface $metadataHelper,
    ) {
    }

    public function guessFormType(OperationInterface $operation, PropertyInterface $property): ?string
    {
        $resource = $operation->getResource();
        $metadata = $this->metadataHelper->findMetadata($resource->getDataClass());

        if ($metadata === null) {
            return $this->formGuesser->guessFormType($operation, $property);
        }
        $reflectionClass = $metadata->getReflectionClass();

        if (!$reflectionClass->hasProperty($property->getPropertyPath())) {
            return $this->formGuesser->guessFormType($operation, $property);
        }
        $reflectionProperty = $reflectionClass->getProperty($property->getPropertyPath());

        if (!empty($reflectionProperty->getAttributes(GeneratedValue::class))) {
            return null;
        }

        return $this->formGuesser->guessFormType($operation, $property);
    }

    public function guessFormOptions(OperationInterface $operation, PropertyInterface $property): array
    {
        return $this->formGuesser->guessFormOptions($operation, $property);
    }
}
