<?php

namespace LAG\AdminBundle\Admin\Behaviors;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

trait AdminTrait
{
    /**
     * Admin name.
     *
     * @var string
     */
    protected $name;

    /**
     * Full namespace for Admin entity.
     *
     * @var string
     */
    protected $entityNamespace;

    /**
     * Form type.
     *
     * @var string
     */
    protected $formType;

    /**
     * Controller.
     *
     * @var Controller
     */
    protected $controller;

    /**
     * @var Pagerfanta
     */
    protected $pager;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var AdminConfiguration
     */
    protected $configuration;

    /**
     * @return object
     */
    public abstract function getUniqueEntity();

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEntityNamespace()
    {
        return $this->entityNamespace;
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return $this->formType;
    }

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
    public function getController()
    {
        return $this->controller;
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
