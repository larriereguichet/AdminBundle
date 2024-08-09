<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class NormalizationProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private NormalizerInterface $normalizer,
        private DenormalizerInterface $denormalizer,
    ) {
    }

    /**
     * Transform a resource object/array into in target object. This is typically used when working with DTOs.
     * The resource transformation is done only if the following conditions are met:
     *   - the operation has an output target type to deserialize to
     *   - the operation is collection and data is an array; other data formats should be handled by its proper provider
     *   - the operation is not a collection and data are either an array or an object of the operation data class.
     */
    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $uriVariables, $context);

        if (!$this->supports($operation, $data)) {
            return $data;
        }
        $normalizedData = $this->normalizer->normalize($data, null, $operation->getNormalizationContext());
        $targetType = $operation->getOutput();

        if ($operation instanceof CollectionOperationInterface) {
            $targetType .= '[]';
        }

        return $this->denormalizer->denormalize(
            $normalizedData,
            $targetType,
            null,
            $operation->getDenormalizationContext()
        );
    }

    private function supports(OperationInterface $operation, mixed $data): bool
    {
        if ($operation->getOutput() === null) {
            return false;
        }

        if ($operation instanceof CollectionOperationInterface && !is_iterable($data)) {
            return false;
        }

        if (!$operation instanceof CollectionOperationInterface && (!\is_object($data) || $data::class !== $operation->getResource()->getDataClass())) {
            return false;
        }

        return true;
    }
}
