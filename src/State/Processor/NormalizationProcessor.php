<?php

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final readonly class NormalizationProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private NormalizerInterface $normalizer,
        private DenormalizerInterface $denormalizer,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation->getInput() === null) {
            $this->processor->process($data, $operation, $uriVariables, $context);

            return;
        }
        $normalizedData = $this->normalizer->normalize($data, null, $operation->getNormalizationContext());
        $denormalizedData = $this->denormalizer->denormalize(
            $normalizedData,
            $operation->getResource()->getDataClass(),
            null,
            $operation->getDenormalizationContext(),
        );

        $this->processor->process($denormalizedData, $operation, $uriVariables, $context);
    }
}
