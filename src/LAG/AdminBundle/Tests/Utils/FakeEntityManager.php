<?php

namespace LAG\AdminBundle\Tests\Utils;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\PessimisticLockException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;

class FakeEntityManager implements EntityManagerInterface
{
    /**
     * Returns the cache API for managing the second level cache regions or NULL if the cache is not enabled.
     *
     * @return \Doctrine\ORM\Cache|null
     */
    public function getCache()
    {
        // TODO: Implement getCache() method.
    }

    /**
     * Gets the database connection object used by the EntityManager.
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        // TODO: Implement getConnection() method.
    }

    /**
     * Gets an ExpressionBuilder used for object-oriented construction of query expressions.
     *
     * Example:
     *
     * <code>
     *     $qb = $em->createQueryBuilder();
     *     $expr = $em->getExpressionBuilder();
     *     $qb->select('u')->from('User', 'u')
     *         ->where($expr->orX($expr->eq('u.id', 1), $expr->eq('u.id', 2)));
     * </code>
     *
     * @return \Doctrine\ORM\Query\Expr
     */
    public function getExpressionBuilder()
    {
        // TODO: Implement getExpressionBuilder() method.
    }

    /**
     * Starts a transaction on the underlying database connection.
     */
    public function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    /**
     * Executes a function in a transaction.
     *
     * The function gets passed this EntityManager instance as an (optional) parameter.
     *
     * {@link flush} is invoked prior to transaction commit.
     *
     * If an exception occurs during execution of the function or flushing or transaction commit,
     * the transaction is rolled back, the EntityManager closed and the exception re-thrown.
     *
     * @param callable $func the function to execute transactionally
     *
     * @return mixed the non-empty value returned from the closure or true instead
     */
    public function transactional($func)
    {
        // TODO: Implement transactional() method.
    }

    /**
     * Commits a transaction on the underlying database connection.
     */
    public function commit()
    {
        // TODO: Implement commit() method.
    }

    /**
     * Performs a rollback on the underlying database connection.
     */
    public function rollback()
    {
        // TODO: Implement rollback() method.
    }

    /**
     * Creates a new Query object.
     *
     * @param string $dql the DQL string
     *
     * @return Query
     */
    public function createQuery($dql = '')
    {
        // TODO: Implement createQuery() method.
    }

    /**
     * Creates a Query from a named query.
     *
     * @param string $name
     *
     * @return Query
     */
    public function createNamedQuery($name)
    {
        // TODO: Implement createNamedQuery() method.
    }

    /**
     * Creates a native SQL query.
     *
     * @param string $sql
     * @param ResultSetMapping $rsm the ResultSetMapping to use
     *
     * @return NativeQuery
     */
    public function createNativeQuery($sql, ResultSetMapping $rsm)
    {
        // TODO: Implement createNativeQuery() method.
    }

    /**
     * Creates a NativeQuery from a named native query.
     *
     * @param string $name
     *
     * @return NativeQuery
     */
    public function createNamedNativeQuery($name)
    {
        // TODO: Implement createNamedNativeQuery() method.
    }

    /**
     * Create a QueryBuilder instance.
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        // TODO: Implement createQueryBuilder() method.
    }

    /**
     * Gets a reference to the entity identified by the given type and identifier
     * without actually loading it, if the entity is not yet loaded.
     *
     * @param string $entityName the name of the entity type
     * @param mixed $id the entity identifier
     *
     * @return object the entity reference
     *
     * @throws ORMException
     */
    public function getReference($entityName, $id)
    {
        // TODO: Implement getReference() method.
    }

    /**
     * Gets a partial reference to the entity identified by the given type and identifier
     * without actually loading it, if the entity is not yet loaded.
     *
     * The returned reference may be a partial object if the entity is not yet loaded/managed.
     * If it is a partial object it will not initialize the rest of the entity state on access.
     * Thus you can only ever safely access the identifier of an entity obtained through
     * this method.
     *
     * The use-cases for partial references involve maintaining bidirectional associations
     * without loading one side of the association or to update an entity without loading it.
     * Note, however, that in the latter case the original (persistent) entity data will
     * never be visible to the application (especially not event listeners) as it will
     * never be loaded in the first place.
     *
     * @param string $entityName the name of the entity type
     * @param mixed $identifier the entity identifier
     *
     * @return object the (partial) entity reference
     */
    public function getPartialReference($entityName, $identifier)
    {
        // TODO: Implement getPartialReference() method.
    }

    /**
     * Closes the EntityManager. All entities that are currently managed
     * by this EntityManager become detached. The EntityManager may no longer
     * be used after it is closed.
     */
    public function close()
    {
        // TODO: Implement close() method.
    }

    /**
     * Creates a copy of the given entity. Can create a shallow or a deep copy.
     *
     * @param object $entity the entity to copy
     * @param bool $deep FALSE for a shallow copy, TRUE for a deep copy
     *
     * @return object the new entity
     *
     * @throws \BadMethodCallException
     */
    public function copy($entity, $deep = false)
    {
        // TODO: Implement copy() method.
    }

    /**
     * Acquire a lock on the given entity.
     *
     * @param object $entity
     * @param int $lockMode
     * @param int|null $lockVersion
     *
     * @throws OptimisticLockException
     * @throws PessimisticLockException
     */
    public function lock($entity, $lockMode, $lockVersion = null)
    {
        // TODO: Implement lock() method.
    }

    /**
     * Gets the EventManager used by the EntityManager.
     *
     * @return \Doctrine\Common\EventManager
     */
    public function getEventManager()
    {
        // TODO: Implement getEventManager() method.
    }

    /**
     * Gets the Configuration used by the EntityManager.
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        // TODO: Implement getConfiguration() method.
    }

    /**
     * Check if the Entity manager is open or closed.
     *
     * @return bool
     */
    public function isOpen()
    {
        // TODO: Implement isOpen() method.
    }

    /**
     * Gets the UnitOfWork used by the EntityManager to coordinate operations.
     *
     * @return UnitOfWork
     */
    public function getUnitOfWork()
    {
        // TODO: Implement getUnitOfWork() method.
    }

    /**
     * Gets a hydrator for the given hydration mode.
     *
     * This method caches the hydrator instances which is used for all queries that don't
     * selectively iterate over the result.
     *
     * @deprecated
     *
     * @param int $hydrationMode
     *
     * @return \Doctrine\ORM\Internal\Hydration\AbstractHydrator
     */
    public function getHydrator($hydrationMode)
    {
        // TODO: Implement getHydrator() method.
    }

    /**
     * Create a new instance for the given hydration mode.
     *
     * @param int $hydrationMode
     *
     * @return \Doctrine\ORM\Internal\Hydration\AbstractHydrator
     *
     * @throws ORMException
     */
    public function newHydrator($hydrationMode)
    {
        // TODO: Implement newHydrator() method.
    }

    /**
     * Gets the proxy factory used by the EntityManager to create entity proxies.
     *
     * @return \Doctrine\ORM\Proxy\ProxyFactory
     */
    public function getProxyFactory()
    {
        // TODO: Implement getProxyFactory() method.
    }

    /**
     * Gets the enabled filters.
     *
     * @return \Doctrine\ORM\Query\FilterCollection the active filter collection
     */
    public function getFilters()
    {
        // TODO: Implement getFilters() method.
    }

    /**
     * Checks whether the state of the filter collection is clean.
     *
     * @return bool true, if the filter collection is clean
     */
    public function isFiltersStateClean()
    {
        // TODO: Implement isFiltersStateClean() method.
    }

    /**
     * Checks whether the Entity Manager has filters.
     *
     * @return bool true, if the EM has a filter collection
     */
    public function hasFilters()
    {
        // TODO: Implement hasFilters() method.
    }

    /**
     * Finds an object by its identifier.
     *
     * This is just a convenient shortcut for getRepository($className)->find($id).
     *
     * @param string $className the class name of the object to find
     * @param mixed $id the identity of the object to find
     *
     * @return object the found object
     */
    public function find($className, $id)
    {
        // TODO: Implement find() method.
    }

    /**
     * Tells the ObjectManager to make an instance managed and persistent.
     *
     * The object will be entered into the database as a result of the flush operation.
     *
     * NOTE: The persist operation always considers objects that are not yet known to
     * this ObjectManager as NEW. Do not pass detached objects to the persist operation.
     *
     * @param object $object the instance to make managed and persistent
     */
    public function persist($object)
    {
        // TODO: Implement persist() method.
    }

    /**
     * Removes an object instance.
     *
     * A removed object will be removed from the database as a result of the flush operation.
     *
     * @param object $object the object instance to remove
     */
    public function remove($object)
    {
        // TODO: Implement remove() method.
    }

    /**
     * Merges the state of a detached object into the persistence context
     * of this ObjectManager and returns the managed copy of the object.
     * The object passed to merge will not become associated/managed with this ObjectManager.
     *
     * @param object $object
     *
     * @return object
     */
    public function merge($object)
    {
        // TODO: Implement merge() method.
    }

    /**
     * Clears the ObjectManager. All objects that are currently managed
     * by this ObjectManager become detached.
     *
     * @param string|null $objectName if given, only objects of this type will get detached
     */
    public function clear($objectName = null)
    {
        // TODO: Implement clear() method.
    }

    /**
     * Detaches an object from the ObjectManager, causing a managed object to
     * become detached. Unflushed changes made to the object if any
     * (including removal of the object), will not be synchronized to the database.
     * Objects which previously referenced the detached object will continue to
     * reference it.
     *
     * @param object $object the object to detach
     */
    public function detach($object)
    {
        // TODO: Implement detach() method.
    }

    /**
     * Refreshes the persistent state of an object from the database,
     * overriding any local changes that have not yet been persisted.
     *
     * @param object $object the object to refresh
     */
    public function refresh($object)
    {
        // TODO: Implement refresh() method.
    }

    /**
     * Flushes all changes to objects that have been queued up to now to the database.
     * This effectively synchronizes the in-memory state of managed objects with the
     * database.
     */
    public function flush()
    {
        // TODO: Implement flush() method.
    }

    /**
     * Gets the repository for a class.
     *
     * @param string $className
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($className)
    {
        // TODO: Implement getRepository() method.
    }

    /**
     * Gets the metadata factory used to gather the metadata of classes.
     *
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadataFactory
     */
    public function getMetadataFactory()
    {
        // TODO: Implement getMetadataFactory() method.
    }

    /**
     * Helper method to initialize a lazy loading proxy or persistent collection.
     *
     * This method is a no-op for other objects.
     *
     * @param object $obj
     */
    public function initializeObject($obj)
    {
        // TODO: Implement initializeObject() method.
    }

    /**
     * Checks if the object is part of the current UnitOfWork and therefore managed.
     *
     * @param object $object
     *
     * @return bool
     */
    public function contains($object)
    {
        // TODO: Implement contains() method.
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method Mapping\ClassMetadata getClassMetadata($className)
    }

    public function getClassMetadata($className)
    {
    }
}
