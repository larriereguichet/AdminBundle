<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Session\FlashMessage;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class FlashMessageHelper
{
    public function __construct(
        private ApplicationConfiguration $appConfig,
        private RequestStack $requestStack,
        private TranslationHelperInterface $translationHelper
    ) {
    }

    public function add(string $type, string $message, array $messageParameters = []): void
    {
        if ($this->appConfig->isTranslationEnabled()) {
            $message = $this
                ->translationHelper
                ->trans($message, $messageParameters, $this->appConfig->getTranslationCatalog())
            ;
        }
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add($type, $message);
    }
}
