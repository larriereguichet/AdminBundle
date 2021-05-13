<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter;

interface FilterInterface
{
    /**
     * Return the filter name. Each filter should have an unique name, or it will be override.
     */
    public function getName(): string;

    /**
     * Return the filter type. It can be a built-in type or a custom type.
     */
    public function getType(): string;

    /**
     * Return the value of the filter, if there is one. When using Doctrine ORM, it can be any value that can be passed
     * to the query builder.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Return the filter comparator operator, for instance "like". When using Doctrine ORM, it can be any comparison
     * operator that can passed to the query builder.
     */
    public function getComparator(): string;

    /**
     * Return the logic operator for the filter. It can be "and" or "or".
     */
    public function getOperator(): string;

    /**
     * Return the object path the filter which will be applied.
     */
    public function getPath(): string;
}
