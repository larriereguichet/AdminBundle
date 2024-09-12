<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Factory;

use LAG\AdminBundle\Exception\InvalidFilterException;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\FilterInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class FilterFactory implements FilterFactoryInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function create(CollectionOperationInterface $operation, FilterInterface $filter): FilterInterface
    {
        $errors = $this->validator->validate($filter, [new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidFilterException($filter->getName(), $errors);
        }

        return $filter;
    }
}
