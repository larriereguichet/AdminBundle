<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Factory;

use LAG\AdminBundle\Exception\Validation\InvalidFilterException;
use LAG\AdminBundle\Metadata\Filter\Filter;
use LAG\AdminBundle\Metadata\Filter\FilterInterface;
use LAG\AdminBundle\Metadata\Filter\StringFilter;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use LAG\AdminBundle\Resource\Metadata\Text;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FilterFactory implements FilterFactoryInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function create(FilterInterface $filterDefinition): FilterInterface
    {
        $filter = $this->initializeFilter($filterDefinition);
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

        if ($property instanceof Text) {
            $class = StringFilter::class;
        }

        $definition = new $class(
            name: $name,
            propertyPath: $property->getPropertyPath(),
        );

        return $this->create($definition);
    }

    private function initializeFilter(FilterInterface $filter): FilterInterface
    {
        if ($filter->getPropertyPath() === null) {
            $filter = $filter->withPropertyPath($filter->getName());
        }

        return $filter;
    }
}
