<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class SerializationProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private SerializerInterface $serializer,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $urlVariables, $context);

        if (
            ($context['json'] ?? false) !== true
            || !$operation->hasAjax()
            || ($operation instanceof CollectionOperationInterface && !\is_array($data))
        ) {
            return $data;
        }
        $type = $operation->getResource()->getResourceClass();

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
