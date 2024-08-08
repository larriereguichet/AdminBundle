<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;

final class DataTransformerRegistry implements DataTransformerRegistryInterface
{
    private array $transformers;

    public function __construct(
        /** @var iterable<DataTransformerInterface> $dataTransformers */
        iterable $dataTransformers,
    ) {
        $this->transformers = [];

        foreach ($dataTransformers as $dataTransformer) {
            $this->transformers[get_class($dataTransformer)] = $dataTransformer;
        }
    }

    public function get(string $dataTransformer): DataTransformerInterface
    {
        if (!$this->has($dataTransformer)) {
            throw new Exception(sprintf('The dataTransformer "%s" does not exist.', $dataTransformer));
        }

        return $this->transformers[$dataTransformer];
    }

    public function has(string $dataTransformer): bool
    {
        return array_key_exists($dataTransformer, $this->transformers);
    }
}
