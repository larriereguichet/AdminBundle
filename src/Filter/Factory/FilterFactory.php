<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Factory;

use LAG\AdminBundle\Exception\InvalidFilterException;
use LAG\AdminBundle\Resource\Metadata\Filter;
use LAG\AdminBundle\Resource\Metadata\FilterInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use LAG\AdminBundle\Resource\Metadata\Text;
use LAG\AdminBundle\Resource\Metadata\TextFilter;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class FilterFactory implements FilterFactoryInterface
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

        if ($property instanceof Text) {
            $class = TextFilter::class;
        }

        $definition = new $class(
            name: $name,
            propertyPath: $property->getPropertyPath(),
        );

        return $this->create($definition);
    }
}
