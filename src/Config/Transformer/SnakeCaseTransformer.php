<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config\Transformer;

use function Symfony\Component\String\u;

final readonly class SnakeCaseTransformer
{
    public function __invoke(object $object, callable $next): mixed
    {
        $result = $next();

        if (!\is_array($result)) {
            return $result;
        }
        $snakeCased = [];

        foreach ($result as $key => $value) {
            if (!\is_string($key)) {
                $snakeCased[$key] = $value;

                continue;
            }
            $snakeCased[u($key)->snake()->toString()] = $value;
        }

        return $snakeCased;
    }
}
