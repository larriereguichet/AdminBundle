<?php

namespace LAG\AdminBundle\Field;

use Exception;
use JK\Configuration\Configuration;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;

abstract class AbstractFieldOLD implements FieldInterface
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
     * @var Configuration
     */
    protected $configuration;
    
    /**
     * @var ActionConfiguration
     */
    protected $actionConfiguration;
    
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
    
    /**
     * Return the value of a configuration parameter.
     *
     * @param string    $name
     *
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getConfiguration($name, $default = null)
    {
        if (!$this->configuration->hasParameter($name)) {
            return $default;
        }
        
        return $this
            ->configuration
            ->getParameter($name)
        ;
    }
    
    /**
     * @param ActionConfiguration $actionConfiguration
     */
    public function setActionConfiguration(ActionConfiguration $actionConfiguration)
    {
        $this->actionConfiguration = $actionConfiguration;
    }
    
    /**
     * @param Configuration $configuration
     *
     * @throws Exception
     */
    public function setConfiguration(Configuration $configuration)
    {
        if (!$configuration->isResolved()) {
            throw new Exception('The configuration must be resolved');
        }

        if (null !== $this->configuration) {
            throw new Exception('The configuration has already been defined');
        }
        $this->configuration = $configuration;
        $this->options = $configuration->getParameters();
    }
}
