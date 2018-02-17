<?php

namespace LAG\AdminBundle\DataProvider\Loader;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Pager\PagerfantaAdminAdapter;
use Pagerfanta\Pagerfanta;

class PaginatedEntityLoader implements EntityLoaderInterface
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * PaginatedEntityLoader constructor.
     *
     * @param DataProviderInterface $dataProvider
     */
    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * Load the entities according to given criteria. An optional pagination can also be used.
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return Collection|Pagerfanta
     */
    public function load(array $criteria, array $orderBy = [], $limit = 25, $offset = 1)
    {
        $adapter = new PagerfantaAdminAdapter($this->dataProvider, $criteria, $orderBy);

        $pager = new Pagerfanta($adapter);
        $pager->setCurrentPage($offset);
        $pager->setMaxPerPage($limit);

        return $pager;
    }

    /**
     * Return the associated DataProvider.
     *
     * @return DataProviderInterface
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }
}
