<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Pagerfanta\Adapter\TransformingAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class DoctrineCollectionNormalizeProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private SerializerInterface $serializer,
    ) {
    }

    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $uriVariables, $context);

        if ($operation->getOutput() === null || !$operation instanceof CollectionOperationInterface) {
            return $data;
        }

        if (!$this->serializer instanceof NormalizerInterface || !$this->serializer instanceof DenormalizerInterface) {
            throw new Exception('The serializer should be an instance of '.NormalizerInterface::class.' to use input');
        }
        $transformer = function (mixed $item) use ($operation) {
            $normalizedData = $this->serializer->normalize($item, null, $operation->getNormalizationContext());

            return $this->serializer->denormalize(
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
