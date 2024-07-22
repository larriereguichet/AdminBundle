<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Resolver;

final readonly class PhpFileResolver implements PhpFileResolverInterface
{
    public function resolveFile(string $path): iterable
    {
        $loader = \Closure::bind(static function ($file) {
            return require $file;
        }, null, null);

        $callback = $loader($path);

        if (!is_callable($callback)) {
            return [];
        }
        $result = $callback();

        if (!is_iterable($result)) {
            return [];
        }

        foreach ($result as $value) {
            yield $value;
        }
    }
}
