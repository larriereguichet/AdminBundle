<?php

namespace LAG\AdminBundle\Admin\Behaviors;

use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Pagerfanta\Pagerfanta;

trait AdminTrait
{
    /**
     * @var Pagerfanta
     */
    protected $pager;

    /**
     * @var AdminConfiguration
     */
    protected $configuration;

    /**
     * @return object
     */
    public abstract function getUniqueEntity();

    /**
     * @return AdminConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return Pagerfanta
     */
    public function getPager()
    {
        return $this->pager;
    }

    /**
     * @return string
     */
    public function getEntityLabel()
    {
        $label = '';
        $entity = $this->getUniqueEntity();

        if (method_exists($entity, 'getLabel')) {
            $label = $entity->getLabel();
        } elseif (method_exists($entity, 'getTitle')) {
            $label = $entity->getTitle();
        } elseif (method_exists($entity, 'getName')) {
            $label = $entity->getName();
        } elseif (method_exists($entity, '__toString')) {
            $label = $entity->__toString();
        }

        return $label;
    }
}
