<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\DataTransformer;

class CallbackTransformer implements DataTransformerInterface
{
    public function __construct(
        private \Closure $callback,
    ) {
    }

    public function transform(mixed $data): mixed
    {
        return ($this->callback)($data);
    }
}
