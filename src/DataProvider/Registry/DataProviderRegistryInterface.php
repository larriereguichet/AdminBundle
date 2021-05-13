<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DataProvider\Registry;

use Exception;
use LAG\AdminBundle\DataProvider\DataProviderInterface;

interface DataProviderRegistryInterface
{
    /**
     * Return an configured data provider or try to create one for the given entity class.
     *
     * @param string $name The name of an existing data provider service
     *
     * @throws Exception
     */
    public function get(string $name): DataProviderInterface;

    /**
     * Return true if a data provider with the given id exists.
     *
     * @param string $name The data provider name
     */
    public function has(string $name): bool;
}
