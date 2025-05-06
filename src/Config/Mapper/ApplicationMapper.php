<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config\Mapper;

use CuyZ\Valinor\MapperBuilder;
use CuyZ\Valinor\Normalizer\Format;
use LAG\AdminBundle\Config\Source\RootSnakeCaseSource;
use LAG\AdminBundle\Config\Transformer\SnakeCaseTransformer;
use LAG\AdminBundle\Metadata\Application;

final class ApplicationMapper
{
    private MapperBuilder $builder;

    public function __construct()
    {
        $this->builder = new MapperBuilder()
            ->enableFlexibleCasting()
            ->allowSuperfluousKeys()
        ;
    }

    public function fromArray(mixed $data): Application
    {
        return $this->builder
            ->mapper()
            ->map(Application::class, RootSnakeCaseSource::array($data))
        ;
    }

    public function toArray(Application $application): array
    {
        return $this->builder
            ->registerTransformer(new SnakeCaseTransformer())
            ->normalizer(Format::array())
            ->normalize($application)
        ;
    }
}
