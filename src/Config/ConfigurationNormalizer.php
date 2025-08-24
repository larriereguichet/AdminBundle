<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config;

use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\FilterInterface;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Metadata\Resource;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ConfigurationNormalizer implements NormalizerInterface, DenormalizerInterface, NormalizerAwareInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;

    public function __construct(
        private readonly NormalizerInterface $objectNormalizer,
        private readonly DenormalizerInterface $objectDenormalizer,
    ) {
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $normalizedData = $this->objectNormalizer->normalize($data, $format, $context);
        $normalizedData['class'] = $data::class;

        if ($data instanceof Resource) {
            $normalizedData['operations'] = $this->normalizer->normalize($data->getOperations(), $format, $context);
            $normalizedData['properties'] = $this->normalizer->normalize($data->getProperties(), $format, $context);
        }

        if ($data instanceof CollectionOperationInterface) {
            $normalizedData['filters'] = $this->normalizer->normalize($data->getFilters(), $format, $context);
            $normalizedData['collection_actions'] = $this->normalizer->normalize($data->getCollectionActions(), $format, $context);
        }

        return $normalizedData;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $class = $data['class'];
        unset($data['class']);

        if (is_a($class, Resource::class, true)) {
            $data['operations'] = $this->denormalizer->denormalize($data['operations'] ?? [], OperationInterface::class.'[]', $format, $context);
            $data['properties'] = $this->denormalizer->denormalize($data['properties'] ?? [], PropertyInterface::class.'[]', $format, $context);
        }

        if (is_a($class, CollectionOperationInterface::class, true)) {
            $data['filters'] = $this->denormalizer->denormalize($data['filters'] ?? [], FilterInterface::class.'[]', $format, $context);

            if ($data['collection_actions'] !== null) {
                $data['collection_actions'] = $this->denormalizer->denormalize($data['collection_actions'], Action::class.'[]', $format, $context);
            }
        }

        if (is_a($class, Grid::class, true)) {
            if ($data['collection_actions'] !== null) {
                $data['collection_actions'] = $this->denormalizer->denormalize($data['collection_actions'], Action::class.'[]', $format, $context);
            }

            if ($data['actions'] !== null) {
                $data['actions'] = $this->denormalizer->denormalize($data['actions'], Action::class.'[]', $format, $context);
            }
        }

        return $this->objectDenormalizer->denormalize($data, $class, $format, $context);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Resource
            || $data instanceof OperationInterface
            || $data instanceof PropertyInterface
            || $data instanceof FilterInterface
            || $data instanceof Grid
        ;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return \in_array($type, [
            Resource::class,
            OperationInterface::class,
            PropertyInterface::class,
            FilterInterface::class,
            Grid::class,
        ], true);
    }

    public function getSupportedTypes(?string $format): array
    {
        return ['*' => false];
    }
}
