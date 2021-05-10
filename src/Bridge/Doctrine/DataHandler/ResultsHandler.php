<?php

namespace LAG\AdminBundle\Bridge\Doctrine\DataHandler;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Bridge\Doctrine\DataSource\ORMDataSource;
use LAG\AdminBundle\DataProvider\DataSourceHandler\DataHandlerInterface;
use LAG\AdminBundle\DataProvider\DataSourceInterface;
use LAG\AdminBundle\Exception\Exception;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Doctrine\Collections\CollectionAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class ResultsHandler implements DataHandlerInterface
{
    public function supports(DataSourceInterface $dataSource): bool
    {
        return $dataSource instanceof ORMDataSource;
    }

    public function handle(DataSourceInterface $dataSource)
    {
        if ($dataSource->isPaginated()) {
            $adapter = $this->getAdapter($dataSource->getData());

            $pager = new Pagerfanta($adapter);
            $pager->setCurrentPage($dataSource->getPage());
            $pager->setMaxPerPage($dataSource->getMaxPerPage());

            return $pager;
        }

        if ($dataSource->getData() instanceof Query) {
            return $dataSource->getData()->getResult();
        }

        if ($dataSource->getData() instanceof QueryBuilder) {
            return $dataSource->getData()->getQuery()->getResult();
        }

        if (\is_array($dataSource->getData())) {
            return new ArrayCollection($dataSource->getData());
        }

        return $dataSource->getData();
    }

    private function getAdapter($data): AdapterInterface
    {
        if ($data instanceof QueryBuilder || $data instanceof Query) {
            return new QueryAdapter($data, true, true);
        }

        if ($data instanceof Collection) {
            return new CollectionAdapter($data);
        }

        if (\is_array($data)) {
            return new ArrayAdapter($data);
        }

        if (is_iterable($data)) {
            return new ArrayAdapter(iterator_to_array($data));
        }

        throw new Exception('Unable to find an adapter for type "'.\gettype($data).'"');
    }
}
