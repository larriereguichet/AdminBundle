<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Session\FlashMessage;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class FlashMessageHelper
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function add(string $type, string $message): void
    {
        /** @var Session $session */
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add($type, $message);
    }
}
