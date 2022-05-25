<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DataProvider;


use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Metadata\Action;

interface DataProviderInterface
{
    public function provide(
        Admin $admin,
        Action $action,
        array $uriVariables= [],
        array $context = []
    ): mixed;



//    /**
//     * Return a collection of entities.
//     */
//    public function getCollectionOLD(
//        string $class,
//        array $criteria = [],
//        array $orderBy = [],
//        int $limit = 1,
//        int $offset = 25
//    ): DataSourceInterface;
//
//    /**
//     * Return a single entity. Throw an exception if no entity was found.
//     *
//     * @throws Exception
//     */
//    public function getOLD(string $class, $identifier): object;
//
//    /**
//     * Create a new entity for the given admin. Return the created entity.
//     */
//    public function create(string $class): object;
}
