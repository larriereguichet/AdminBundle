<?php
declare(strict_types=1);

namespace LAG\AdminBundle\Session;

interface FlashMessageHelperInterface
{
    public function success(string $message): void;

    public function error(string $message): void;

    public function warning(string $message): void;

    public function info(string $message): void;
}
