<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class JsonDataProviderDecorator implements DataProviderInterface
{
    public function __construct(
        private DataProviderInterface $decorated,
        private SerializerInterface $serializer,
    ) {
    }

    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data = $this->decorated->provide($operation, $uriVariables, $context);

        if (($context['json'] ?? false) && $operation->useAjax()) {
            $type = $operation->getResource()->getDataClass();

            if ($operation instanceof CollectionOperationInterface) {
                $type .= '[]';
            }
            $data = $this->serializer->deserialize(
                $data,
                $type,
                'json',
                $operation->getNormalizationContext() + [AbstractNormalizer::OBJECT_TO_POPULATE => $data],
            );
        }

        return $data;
    }
}
