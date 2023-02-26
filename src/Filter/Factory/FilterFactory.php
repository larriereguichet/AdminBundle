<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Factory;

use LAG\AdminBundle\Exception\Validation\InvalidFilterException;
use LAG\AdminBundle\Metadata\Filter\Filter;
use LAG\AdminBundle\Metadata\Filter\FilterInterface;
use LAG\AdminBundle\Metadata\Filter\StringFilter;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use LAG\AdminBundle\Metadata\Property\StringProperty;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FilterFactory implements FilterFactoryInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function create(FilterInterface $filter): FilterInterface
    {
        $errors = $this->validator->validate($filter, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidFilterException($filter->getName(), $errors);
        }

        return $filter;
    }

    public function createFromProperty(PropertyInterface $property): FilterInterface
    {
        $class = Filter::class;
        $name = $property->getName();

        if ($property instanceof StringProperty) {
            $class = StringFilter::class;
        }

        return new $class(
            name: $name,
            propertyPath: $property->getPropertyPath(),
        );
    }
}
