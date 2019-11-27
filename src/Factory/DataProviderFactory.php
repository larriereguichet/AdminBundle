<?php

namespace LAG\AdminBundle\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use LAG\AdminBundle\DataProvider\DataProviderInterface;

class DataProviderFactory
{
    /**
     * The loaded data providers.
     *
     * @var DataProviderInterface[]
     */
    protected $dataProviders = [];

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * DataProviderFactory constructor.
     *
     * @param EntityManagerInterface $entityManager A Doctrine ORM entity manager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Add a data provider to the collection.
     *
     * @param string $id The data provider service id
     * @param DataProviderInterface $dataProvider The data provider
     *
     * @throws Exception
     */
    public function add($id, DataProviderInterface $dataProvider)
    {
        if ($this->has($id)) {
            throw new Exception('Trying to add the data provider '.$id.' twice');
        }
        // add the data provider to collection, indexed by ids
        $this->dataProviders[$id] = $dataProvider;
    }

    /**
     * Return an configured data provider or try to create one for the given entity class.
     *
     * @param string $id The id of an existing data provider service
     * registry
     *
     * @throws Exception
     */
    public function get(string $id): DataProviderInterface
    {
        if (!$this->has($id)) {
            throw new Exception('No data provider with id "'.$id.'" was loaded');
        }

        return $this->dataProviders[$id];
    }

    /**
     * Return true if a data provider with the given id exists.
     *
     * @param string $id The data provider id
     *
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->dataProviders);
    }
}
