<?php

namespace LAG\AdminBundle\DataProvider\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use LAG\AdminBundle\DataProvider\DataProvider;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Repository\RepositoryInterface;

class DataProviderFactory
{
    /**
     * The loaded data providers
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
     * @param string|null id The id of an existing data provider service
     * @param string|null $entityClass The class of the related entity. It will be used to find a repository in Doctrine
     * registry
     *
     * @return DataProviderInterface
     *
     * @throws Exception
     */
    public function get($id = null, $entityClass = null)
    {
        if (null === $id && null === $entityClass) {
            throw new Exception('You should either provide an data provider or a entity class to get a data provider');
        }

        if (null !== $id && $this->has($id)) {
            // a name is provided and the data provider exist, so we return the found data provider
            $dataProvider = $this->dataProviders[$id];
        } else {
            // no name was provided, so we try to create a generic data provider with th given entity class
            $dataProvider = $this->create($entityClass);
        }

        return $dataProvider;
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

    /**
     * Create a generic data provider.
     *
     * @param string $entityClass The class of the related entity
     *
     * @return DataProviderInterface The created data provider
     *
     * @throws Exception An exception is thrown if the found repository with given entity class does not implements
     * RepositoryInterface.
     */
    public function create($entityClass)
    {
        // get the repository corresponding to the given entity class
        $repository = $this
            ->entityManager
            ->getRepository($entityClass);

        // the repository should implements the RepositoryInterface, to ensure it has the methods create and save
        if (!($repository instanceof RepositoryInterface)) {
            $repositoryClass = get_class($repository);

            throw new Exception(
                sprintf(
                    'Repository %s should implements %s',
                    $repositoryClass,
                    RepositoryInterface::class
                )
            );
        }
        // create a new generic data provider from the found repository
        $dataProvider = new DataProvider($repository, $this->entityManager);

        return $dataProvider;
    }
}
