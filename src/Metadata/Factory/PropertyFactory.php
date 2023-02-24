<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Exception\Validation\InvalidPropertyException;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PropertyFactory implements PropertyFactoryInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function create(PropertyInterface $property): PropertyInterface
    {
        $errors = $this->validator->validate($property, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidPropertyException($property->getName(), $errors);
        }

        return $property;
    }
}
