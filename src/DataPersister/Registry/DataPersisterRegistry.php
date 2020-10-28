<?php

namespace LAG\AdminBundle\DataPersister\Registry;

use Iterator;
use LAG\AdminBundle\DataPersister\DataPersisterInterface;
use LAG\AdminBundle\Exception\DataProvider\MissingDataProviderException;

class DataPersisterRegistry implements DataPersisterRegistryInterface
{
    private array $dataProviders;

    /**
     * DataProviderRegistry constructor.
     *
     * @param iterable|Iterator $dataProviders
     */
    public function __construct(iterable $dataProviders)
    {
        $this->dataProviders = iterator_to_array($dataProviders);
    }

    public function get(string $name): DataPersisterInterface
    {
        if (!$this->has($name)) {
            throw new MissingDataProviderException('The data provider "'.$name.'" does not exists');
        }

        return $this->dataProviders[$name];
    }

    public function has(string $name): bool
    {
        return key_exists($name, $this->dataProviders);
    }
}
