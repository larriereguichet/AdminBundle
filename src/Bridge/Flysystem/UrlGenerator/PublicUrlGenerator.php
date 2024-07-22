<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Flysystem\UrlGenerator;

use League\Flysystem\Config;
use function Symfony\Component\String\u;

final readonly class PublicUrlGenerator implements \League\Flysystem\UrlGeneration\PublicUrlGenerator
{
    public function __construct(
        private string $mediaDirectory,
    ) {
    }

    public function publicUrl(string $path, Config $config): string
    {
        return u($this->mediaDirectory)->ensureEnd('/')
            ->append($path)
            ->toString()
        ;
    }
}
