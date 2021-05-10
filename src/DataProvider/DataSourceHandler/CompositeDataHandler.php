<?php

namespace LAG\AdminBundle\DataProvider\DataSourceHandler;

use LAG\AdminBundle\DataProvider\DataSourceInterface;
use LAG\AdminBundle\Exception\DataHandler\ClassNotSupportedException;

class CompositeDataHandler implements DataHandlerInterface
{
    /**
     * @var iterable<DataHandlerInterface>
     */
    private iterable $dataHandlers;

    public function __construct(iterable $dataHandlers)
    {
        $this->dataHandlers = $dataHandlers;
    }

    public function supports(DataSourceInterface $dataSource): bool
    {
        foreach ($this->dataHandlers as $dataHandler) {
            if ($dataHandler->supports($dataSource)) {
                return true;
            }
        }

        return false;
    }

    public function handle(DataSourceInterface $dataSource)
    {
        foreach ($this->dataHandlers as $dataHandler) {
            if ($dataHandler->supports($dataSource)) {
                return $dataHandler->handle($dataSource);
            }
        }

        throw new ClassNotSupportedException($dataSource);
    }
}
