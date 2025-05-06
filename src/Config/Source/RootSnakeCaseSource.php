<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config\Source;

use function Symfony\Component\String\u;

final readonly class RootSnakeCaseSource implements \IteratorAggregate
{
    private array $source;

    public static function array(array $data): self
    {
        return new self($data);
    }

    private function __construct(
        array $source,
    ) {
        $this->source = $this->toCamelCase($source);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->source;
    }

    private function toCamelCase(array $source): array
    {
        $camelSource = [];

        foreach ($source as $key => $value) {
            if (!\is_string($key) || u($key)->startsWith('__')) {
                $camelSource[$key] = $value;

                continue;
            }
            $camelCaseKey = u($key)->camel()->toString();
            $camelSource[$camelCaseKey] = $value;
        }

        return $camelSource;
    }
}
