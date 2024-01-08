<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Exception\InvalidPropertyCollectionException;
use LAG\AdminBundle\Metadata\Property\ResourceLink;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Property\CollectionPropertyInterface;
use LAG\AdminBundle\Metadata\Property\CompositePropertyInterface;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function Symfony\Component\String\u;

readonly class PropertyFactory implements PropertyFactoryInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function createCollection(Resource $resource): array
    {
        $operationErrors = [];
        $properties = [];

        foreach ($resource->getProperties() as $property) {
            $property = $this->createProperty($resource, $property);
            $errors = $this->validator->validate($property, [new Valid()]);

            if ($errors->count() > 0) {
                $operationErrors[$property->getName()] = $errors;
            }
            $properties[$property->getName()] = $property;
        }

        if (\count($operationErrors) > 0) {
            throw new InvalidPropertyCollectionException(
                $operationErrors,
                $resource->getName(),
            );
        }

        return $properties;
    }

    public function createProperty(Resource $resource, PropertyInterface $property): PropertyInterface
    {
        if (!$property->getPropertyPath()) {
            $property = $property->withPropertyPath($property->getName());
        }

        if (!$property->getLabel()) {
            if ($resource->getTranslationPattern()) {
                $label = u($resource->getTranslationPattern())
                    ->replace('{application}', $resource->getApplicationName())
                    ->replace('{resource}', $resource->getName())
                    ->replace('{message}', u($property->getName())->snake()->toString())
                    ->lower()
                    ->toString()
                ;
            } else {
                $label = u($property->getName())
                    ->replace('_', ' ')
                    ->title()
                    ->toString()
                ;
            }
            $property = $property->withLabel($label);
        }

        if (!$property->getTranslationDomain()) {
            $property = $property->withTranslationDomain($resource->getTranslationDomain());
        }

        if ($property instanceof CompositePropertyInterface) {
            $children = [];

            foreach ($property->getChildren() as $index => $child) {
                $children[$index] = $this->createProperty($resource, $child);
            }
            $property = $property->withChildren($children);
        }

        if ($property instanceof CollectionPropertyInterface) {
            $property = $property->withPropertyType($this->createProperty($resource, $property->getPropertyType()));
        }

        if ($property instanceof ResourceLink) {
            if ($property->getApplication() === null) {
                $property = $property->withApplication($resource->getApplicationName());
            }

            if ($property->getResource() === null) {
                $property = $property->withResource($resource->getName());
            }
        }

        return $property;
    }
}
