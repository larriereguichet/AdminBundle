<?php

namespace LAG\AdminBundle\Filter;

class Filter
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $operator;

    /**
     * Filter constructor.
     *
     * @param string $name
     * @param mixed  $value
     * @param string $operator
     */
    public function __construct(string $name, $value, string $operator = '=')
    {
        $this->name = $name;
        $this->value = $value;
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }
}
