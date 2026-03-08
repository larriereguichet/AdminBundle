<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config;

use LAG\AdminBundle\Metadata\ApplicationInterface;
use LAG\AdminBundle\Metadata\ApplicationMetadataInterface;
use LAG\AdminBundle\Metadata\Attribute\Application;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\GridMetadataInterface;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

// TODO remove
final readonly class ConfigurationMapper
{
    private NormalizerInterface $normalizer;
    private DenormalizerInterface $denormalizer;

    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $objectNormalizer = new ObjectNormalizer(
            classMetadataFactory: $classMetadataFactory,
            nameConverter: new CamelCaseToSnakeCaseNameConverter()
        );
        $configurationNormalizer = new ConfigurationNormalizer(
            objectNormalizer: $objectNormalizer,
            objectDenormalizer: $objectNormalizer,
        );
        $arrayNormalizer = new ArrayDenormalizer();
        $serializer = new Serializer(normalizers: [$configurationNormalizer, $objectNormalizer, $arrayNormalizer]);
        $configurationNormalizer->setNormalizer($serializer);
        $configurationNormalizer->setDenormalizer($serializer);
        $arrayNormalizer->setDenormalizer($serializer);

        $this->normalizer = $serializer;
        $this->denormalizer = $serializer;
    }

    /** @return array<string, mixed> */
    public function fromApplication(ApplicationInterface $application): array
    {
        return $this->normalizer->normalize($application);
    }

    /** @param array<string, mixed> $data */
    public function toApplication(array $data): ApplicationMetadataInterface
    {
        return $this->denormalizer->denormalize($data, Application::class);
    }

    /** @return array<string, mixed> */
    public function fromResource(ResourceMetadataInterface $resource): array
    {
        return $this->normalizer->normalize($resource);
    }

    /** @param array<string, mixed> $data */
    public function toResource(array $data): ResourceMetadataInterface
    {
        return $this->denormalizer->denormalize($data, Resource::class);
    }

    /** @return array<string, mixed> */
    public function fromGrid(GridMetadataInterface $grid): array
    {
        return $this->normalizer->normalize($grid);
    }

    /** @param array<string, mixed> $data */
    public function toGrid(array $data): GridMetadataInterface
    {
        return $this->denormalizer->denormalize($data, Grid::class);
    }
}
