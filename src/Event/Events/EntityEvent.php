<?php

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Event\AbstractEvent;

class EntityEvent extends AbstractEvent
{
    /**
     * @var mixed
     */
    private $entities;

    /**
     * @var array
     */
    private $filters = [];

    /**
     * @return mixed
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param mixed $entities
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }
}
