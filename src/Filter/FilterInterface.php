<?php

namespace LAG\AdminBundle\Filter;

interface FilterInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @return string
     */
    public function getOperator(): string;
}
