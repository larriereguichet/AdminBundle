<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Pagerfanta\Adapter\TransformingAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class DoctrineCollectionNormalizeProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private NormalizerInterface $normalizer,
        private DenormalizerInterface $denormalizer,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $urlVariables, $context);

        if ($operation->getOutput() === null || !$operation instanceof CollectionOperationInterface) {
            return $data;
        }

        $transformer = function (mixed $item) use ($operation) {
            $normalizedData = $this->normalizer->normalize($item, null, $operation->getNormalizationContext());

            return $this->denormalizer->denormalize(
                $normalizedData,
                $operation->getOutput(),
                null,
                $operation->getDenormalizationContext(),
            );
        };

        if ($data instanceof Collection) {
            return $data->map($transformer);
        }

        if ($data instanceof PagerfantaInterface) {
            $pager = new Pagerfanta(new TransformingAdapter($data->getAdapter(), $transformer));
            $pager->setCurrentPage($data->getCurrentPage());
            $pager->setMaxPerPage($data->getMaxPerPage());

            return $pager;
        }

        return $data;
    }
}
