<?php

namespace LAG\AdminBundle\Admin\Behaviors;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\ManagerInterface;
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
     * Entity repository.
     *
     * @var EntityRepository
     */
    protected $repository;

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
     * Entity manager (doctrine entity manager by default).
     *
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @var AdminConfiguration
     */
    protected $configuration;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getEntityNamespace()
    {
        return $this->entityNamespace;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return mixed
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @return ManagerInterface
     */
    public function getManager()
    {
        return $this->manager;
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
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    public function getEntityLabel()
    {
        $label = '';
        $entity = $this->getEntity();

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
