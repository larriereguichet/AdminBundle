<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

use LAG\AdminBundle\Metadata\Attribute\Action;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\UX\TwigComponent\Attribute\PreMount;

final class GridActions
{
    /** @var iterable<int, Action> */
    public iterable $actions = [];

    /** @param array<int|string, mixed> $data */
    #[PreMount]
    public function validate(array $data): void
    {
        $data['actions'] = $data['actions'] ?? [];

        foreach ($data['actions'] as $action) {
            if (!$action instanceof Action) {
                throw new UnexpectedTypeException($action, Action::class);
            }
        }
    }
}
