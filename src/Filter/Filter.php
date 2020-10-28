<?php

namespace LAG\AdminBundle\Filter;

class Filter implements FilterInterface
{
    private string $name;
    private $value;
    private string $operator;
    private string $comparator;

    public function __construct(string $name, $value, string $comparator = 'like', string $operator = 'or')
    {
        $this->name = $name;
        $this->value = $value;
        $this->operator = $operator;
        $this->comparator = $comparator;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function getComparator(): string
    {
        return $this->comparator;
    }

    /**
     * {@inheritdoc}
     */
    public function getOperator(): string
    {
        return $this->operator;
    }
}
