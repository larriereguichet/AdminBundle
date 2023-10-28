<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Exception\Validation\InvalidPropertyCollectionException;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function Symfony\Component\String\u;

class PropertyFactory implements PropertyFactoryInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function createCollection(OperationInterface $operation): array
    {
        $operationErrors = [];
        $properties = [];

        foreach ($operation->getProperties() as $property) {
            $property = $this->initializeProperty($operation, $property);
            $errors = $this->validator->validate($property, [new Valid()]);

            if ($errors->count() > 0) {
                $operationErrors[$property->getName()] = $errors;
            }
            $properties[$property->getName()] = $property;
        }

        if (\count($operationErrors) > 0) {
            throw new InvalidPropertyCollectionException(
                $operationErrors,
                $operation->getResource()->getName(),
                $operation->getName()
            );
        }

        return $properties;
    }

    private function initializeProperty(OperationInterface $operation, PropertyInterface $property): PropertyInterface
    {
        if (!$property->getPropertyPath()) {
            $property = $property->withPropertyPath($property->getName());
        }

        if (!$property->getLabel()) {
            if ($operation->getResource()->getTranslationPattern()) {
                $label = u($operation->getResource()->getTranslationPattern())
                    ->replace('{application}', $operation->getResource()->getApplicationName())
                    ->replace('{resource}', $operation->getResource()->getName())
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
            $property = $property->withTranslationDomain($operation->getResource()->getTranslationDomain());
        }

        return $property;
    }
}
