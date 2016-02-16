<?php

namespace LAG\AdminBundle\Admin\Configuration;


class ActionConfiguration
{
    protected $route;

    protected $parameters;

    protected $loadStrategy;

    protected $exports;

    protected $order;

    protected $target;

    protected $icon;

    protected $batch;

    /**
     * Indicate which pager should be used. If null, no pager will be used.
     *
     * @var string
     */
    protected $pager;

    /**
     * Action criteria. Entities will be filtered by thoses criteria.
     *
     * @var array
     */
    protected $criteria;

    /**
     * ActionConfiguration constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->loadStrategy = $configuration['load_strategy'];
        $this->route = $configuration['route'];
        $this->parameters = $configuration['parameters'];
        $this->exports = $configuration['export'];
        $this->order = $configuration['order'];
        $this->target = $configuration['target'];
        $this->icon = $configuration['icon'];
        $this->batch = $configuration['batch'];
        $this->pager = $configuration['pager'];
        $this->criteria = $configuration['criteria'];
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param mixed $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return mixed
     */
    public function getLoadStrategy()
    {
        return $this->loadStrategy;
    }

    /**
     * @param mixed $loadStrategy
     */
    public function setLoadStrategy($loadStrategy)
    {
        $this->loadStrategy = $loadStrategy;
    }

    /**
     * @return mixed
     */
    public function getExports()
    {
        return $this->exports;
    }

    /**
     * @param mixed $exports
     */
    public function setExports($exports)
    {
        $this->exports = $exports;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param mixed $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @param mixed $batch
     */
    public function setBatch($batch)
    {
        $this->batch = $batch;
    }

    /**
     * Return which pager should be used for this action. If null, pager is not required.
     *
     * @return string
     */
    public function getPager()
    {
        return $this->pager;
    }

    /**
     * Return action critera.
     *
     * @return array
     */
    public function getCriteria()
    {
        return $this->criteria;
    }


}
