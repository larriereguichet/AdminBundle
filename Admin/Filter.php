<?php

namespace LAG\AdminBundle\Admin;

class Filter
{
    const TYPE_SELECT = 'select';

    /**
     * Field name.
     *
     * @var string
     */
    protected $fieldName;

    /**
     * Filter type (select, text...).
     *
     * @var
     */
    protected $type;

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
