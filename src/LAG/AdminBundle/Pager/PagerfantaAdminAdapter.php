<?php

namespace LAG\AdminBundle\Pager;

use LAG\AdminBundle\DataProvider\DataProviderInterface;
use Pagerfanta\Adapter\AdapterInterface;

class PagerfantaAdminAdapter implements AdapterInterface
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var array
     */
    private $criteria;

    /**
     * @var array
     */
    private $orderBy;
    
    /**
     * @var array
     */
    private $options;
    
    private $data;
    
    /**
     * PagerfantaAdminAdapter constructor.
     *
     * @param DataProviderInterface $dataProvider
     * @param array                 $criteria
     * @param array                 $orderBy
     * @param array                 $options
     */
    public function __construct(
        DataProviderInterface $dataProvider,
        array $criteria = [],
        array $orderBy = [],
        array $options = []
    ) {
        $this->dataProvider = $dataProvider;
        $this->criteria = $criteria;
        $this->orderBy = $orderBy;
        $this->options = $options;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults()
    {
        return $this
            ->dataProvider
            ->count($this->criteria)
        ;
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
        $this->retrieveData($length, $offset);
    
        return $this->data;
    }
    
    private function retrieveData($length, $offset)
    {
        $this->data = $this
            ->dataProvider
            ->findBy($this->criteria, $this->orderBy, $length, $offset)
        ;
    }
}
