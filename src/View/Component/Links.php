<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

use LAG\AdminBundle\Metadata\Attribute\Link;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\UX\TwigComponent\Attribute\PreMount;

final class Links
{
    /** @var iterable<int, Link> */
    public iterable $links = [];

    /** @param iterable<int|string, mixed> $data */
    #[PreMount]
    public function validate(iterable $data): void
    {
        $data['links'] ??= [];

        foreach ($data['links'] as $link) {
            if (!$link instanceof Link) {
                throw new UnexpectedTypeException($link, Link::class);
            }
        }
    }
}
