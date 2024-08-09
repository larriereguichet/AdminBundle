<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Session;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final readonly class FlashMessageHelper implements FlashMessageHelperInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function success(string $message): void
    {
        $this->getFlashBag()->add('success', $message);
    }

    public function error(string $message): void
    {
        $this->getFlashBag()->add('error', $message);
    }

    public function warning(string $message): void
    {
        $this->getFlashBag()->add('warning', $message);
    }

    public function info(string $message): void
    {
        $this->getFlashBag()->add('info', $message);
    }

    private function getFlashBag(): FlashBagInterface
    {
        return $this->requestStack->getMainRequest()->getSession()->getFlashBag(); // @phpstan-ignore-line
    }
}
