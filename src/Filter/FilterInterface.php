<?php

namespace LAG\AdminBundle\Filter;

interface FilterInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string
     */
    public function getComparator(): string;

    /**
     * @return string
     */
    public function getOperator(): string;
}
