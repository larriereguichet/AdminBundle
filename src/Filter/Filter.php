<?php

namespace LAG\AdminBundle\Filter;

class Filter implements FilterInterface
{
    private string $name;
    private $value;
    private string $operator;
    private string $comparator;
    private string $type;
    private string $path;

    public function __construct(
        string $name,
        $value,
        string $type,
        string $path,
        string $comparator = 'like',
        string $operator = 'or'
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
        $this->path = $path;
        $this->operator = $operator;
        $this->comparator = $comparator;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getComparator(): string
    {
        return $this->comparator;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
