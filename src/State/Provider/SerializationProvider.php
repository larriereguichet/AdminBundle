<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class SerializationProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private SerializerInterface $serializer,
    ) {
    }

    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $uriVariables, $context);

        if (
            ($context['json'] ?? false) !== true
            || !$operation->useAjax()
            || ($operation instanceof CollectionOperationInterface && !\is_array($data))
        ) {
            return $data;
        }
        $type = $operation->getResource()->getDataClass();

        if ($operation instanceof CollectionOperationInterface && \is_array($data)) {
            $type .= '[]';
        }

        return $this->serializer->deserialize(
            $data,
            $type,
            'json',
            $operation->getNormalizationContext() + [AbstractNormalizer::OBJECT_TO_POPULATE => $data],
        );
    }
}
