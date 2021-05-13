<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Session\FlashMessage;

interface FlashMessageHelperInterface
{
    public function add(string $type, string $message, array $messageParameters = []): void;
}
