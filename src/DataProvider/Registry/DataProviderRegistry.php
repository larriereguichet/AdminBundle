<?php

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
        foreach ($dataProviders as $dataProvider) {
            $this->dataProviders[$dataProvider->getName()] = $dataProvider;
        }
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
