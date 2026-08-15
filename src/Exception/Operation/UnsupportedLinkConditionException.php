<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Operation;

use LAG\AdminBundle\Exception\Exception;

final class UnsupportedLinkConditionException extends Exception
{
    public function __construct(string $operationName, string $linkName, string $links)
    {
        parent::__construct(\sprintf(
            'The link "%s" of the operation "%s" declares a condition, which is only evaluated on item links. A "%s" '
            .'link is rendered once for the whole collection, with no row to evaluate the condition against, so the '
            .'condition would be silently ignored. Remove it, or move the link to the item links.',
            $linkName,
            $operationName,
            $links,
        ));
    }
}
