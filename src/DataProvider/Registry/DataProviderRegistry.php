<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DataProvider\Registry;

use Iterator;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Exception\DataProvider\MissingDataProviderException;

class DataProviderRegistry implements DataProviderRegistryInterface
{
    private array $dataProviders = [];

    /**
     * DataProviderRegistry constructor.
     *
     * @param iterable|Iterator $dataProviders
     */
    public function __construct(iterable $dataProviders)
    {
        $this->dataProviders = iterator_to_array($dataProviders);
    }

    public function get(string $name): DataProviderInterface
    {
        if (!$this->has($name)) {
            throw new MissingDataProviderException('The data provider "'.$name.'" does not exists');
        }

        return $this->dataProviders[$name];
    }

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->dataProviders);
    }
}
