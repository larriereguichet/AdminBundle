<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Filter\Filter;
use Symfony\Contracts\EventDispatcher\Event;

class ORMFilterEvent extends Event
{
    private $data;

    /**
     * @var AdminInterface
     */
    private $admin;

    /**
     * @var Filter[]
     */
    private $filters;

    /**
     * ORMFilterEvent constructor.
     */
    public function __construct($data, AdminInterface $admin, array $filters = [])
    {
        $this->data = $data;
        $this->admin = $admin;
        $this->filters = $filters;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }
}
