<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Filter;

class StringFilter extends Filter
{
    public function __construct(
        string $name,
        ?string $propertyPath = null,
        string $comparator = 'like',
        string $operator = 'and',
    ) {
        parent::__construct(
            $name,
            $propertyPath,
            $comparator,
            $operator,
        );
    }
}
