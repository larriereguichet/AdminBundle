<?php

namespace LAG\AdminBundle\View;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use Pagerfanta\Pagerfanta;

interface ViewInterface
{
    /**
     * Return the Twig template associated to the view.
     */
    public function getTemplate(): string;

    /**
     * @return ActionConfiguration
     */
    public function getConfiguration();

    public function getName();

    public function getActionName();

    /**
     * @return Collection|Pagerfanta|array
     */
    public function getEntities();

    public function setEntities($entities);

    /**
     * @return AdminConfiguration
     */
    public function getAdminConfiguration();

    /**
     * @return bool
     */
    public function haveToPaginate();

    /**
     * @return int
     */
    public function getTotalCount();

    /**
     * @return Pagerfanta|null
     */
    public function getPager();

    public function getData();

    public function getForms();

    /**
     * @return string
     */
    public function getBase();
}
