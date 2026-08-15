<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use LAG\AdminBundle\Mapper\ObjectMapperInterface;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Pagerfanta\Adapter\TransformingAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;

final readonly class MappingProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $urlVariables, $context);

        if (!\is_object($data) || $operation->getOutput() === null) {
            return $data;
        }

        if ($operation instanceof CollectionOperationInterface) {
            if ($data instanceof PagerfantaInterface) {
                $mapper = fn (mixed $item) => \is_object($item)
                    ? $this->objectMapper->map($item, $operation->getOutput())
                    : $item;

                $pager = new Pagerfanta(new TransformingAdapter($data->getAdapter(), $mapper));
                $pager->setCurrentPage($data->getCurrentPage());
                $pager->setMaxPerPage($data->getMaxPerPage());

                return $pager;
            }

            if (is_iterable($data)) {
                $result = [];
                foreach ($data as $item) {
                    $result[] = \is_object($item) ? $this->objectMapper->map($item, $operation->getOutput()) : $item;
                }

                return $result;
            }
        }

        return $this->objectMapper->map($data, $operation->getOutput());
    }
}
