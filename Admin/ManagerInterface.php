<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\ORM\EntityRepository;

interface ManagerInterface
{
    public function create();

    public function delete($entity);

    public function save($entity);

    /**
     * @return EntityRepository
     */
    public function getRepository();

    /**
     * @return string
     */
    public function getClassName();
}
