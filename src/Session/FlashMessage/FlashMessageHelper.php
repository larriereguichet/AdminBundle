<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Session\FlashMessage;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FlashMessageHelper
{
    public function __construct(
        private ApplicationConfiguration $applicationConfiguration,
        private RequestStack $requestStack,
        private TranslationHelperInterface $translationHelper
    ) {
    }

    public function add(string $type, string $message, array $messageParameters = []): void
    {
        if ($this->applicationConfiguration->isTranslationEnabled()) {
            $message = $this
                ->translationHelper
                ->translate($message, $messageParameters, $this->applicationConfiguration->getTranslationDomain())
            ;
        }
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add($type, $message);
    }
}
