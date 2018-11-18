<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractField implements FieldInterface
{
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
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
     * @var bool
     */
    protected $frozen = false;
    
    /**
     * Field constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getOption(string $name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new Exception('Invalid option "'.$name.'" for field "'.$this->name.'"');
        }

        return $this->options[$name];
    }

    public function configureOptions(OptionsResolver $resolver, ActionConfiguration $actionConfiguration)
    {
    }

    /**
     * @param array $options
     *
     * @throws Exception
     */
    public function setOptions(array $options)
    {
        if ($this->frozen) {
            throw new Exception('The options for the field "'.$this->name.'"');
        }
        $this->options = $options;
        $this->frozen = true;
    }
}
