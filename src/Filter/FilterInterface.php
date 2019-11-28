<?php

namespace LAG\AdminBundle\Filter;

interface FilterInterface
{
    public function getName(): string;

    /**
     * @return mixed
     */
    public function getValue();

    public function getComparator(): string;

    public function getOperator(): string;
}
