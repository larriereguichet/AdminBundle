<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

// TODO remove ?
final class MissingGridException extends Exception
{
    public function __construct(string $gridName)
    {
        parent::__construct('The grid "%s" does not exist', $gridName);
    }
}
