<?php

namespace LAG\AdminBundle\Pager;

use LAG\AdminBundle\DataProvider\DataProviderInterface;
use Pagerfanta\Adapter\AdapterInterface;

class PagerFantaAdminAdapter implements AdapterInterface
{
    /**
     * @var DataProviderInterface
     */
    protected $dataProvider;

    /**
     * @var array
     */
    protected $criteria;

    /**
     * @var array
     */
    protected $orderBy;

    /**
     * PagerFantaAdminAdapter constructor.
     *
     * @param DataProviderInterface $dataProvider
     * @param array $criteria
     * @param array $orderBy
     */
    public function __construct(DataProviderInterface $dataProvider, $criteria = [], $orderBy = [])
    {
        $this->dataProvider = $dataProvider;
        $this->criteria = $criteria;
        $this->orderBy = $orderBy;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults()
    {
        $entities = $this
            ->dataProvider
            ->findBy($this->criteria);
        $count = count($entities);
        unset($entities);

        return $count;
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        return $this
            ->dataProvider
            ->findBy($this->criteria, $this->orderBy, $length, $offset);
    }
}
