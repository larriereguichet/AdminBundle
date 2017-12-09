<?php

namespace LAG\AdminBundle\Field;

abstract class AbstractField implements FieldInterface
{
    const TYPE_STRING = 'string';
    const TYPE_LINK = 'link';
    const TYPE_ARRAY = 'array';
    const TYPE_DATE = 'date';
    const TYPE_COUNT = 'count';
    const TYPE_ACTION = 'action';
    const TYPE_COLLECTION = 'collection';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_MAPPED = 'mapped';
    const TYPE_ACTION_COLLECTION = 'action_collection';
    const TYPE_HEADER = 'header';
    
    /**
     * Field's name
     *
     * @var string
     */
    protected $name;
    
    /**
     * @var array
     */
    protected $options = [];
    
    /**
     * Field constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
