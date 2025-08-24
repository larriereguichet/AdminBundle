<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config;

use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\Resource;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

    public function fromApplication(Application $application): array
    {
        return $this->normalizer->normalize($application);
    }

    public function toApplication(array $data): Application
    {
        return $this->denormalizer->denormalize($data, Application::class);
    }

    public function fromResource(Resource $resource): array
    {
        return $this->normalizer->normalize($resource);
    }

    public function toResource(array $data): Resource
    {
        return $this->denormalizer->denormalize($data, Resource::class);
    }

    public function fromGrid(Grid $grid): array
    {
        return $this->normalizer->normalize($grid);
    }

    public function toGrid(array $data): Grid
    {
        return $this->denormalizer->denormalize($data, Grid::class);
    }
}
