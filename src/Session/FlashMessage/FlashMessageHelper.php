<?php

namespace LAG\AdminBundle\Session\FlashMessage;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Translation\Helper\TranslationHelperInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FlashMessageHelper implements FlashMessageHelperInterface
{
    private ApplicationConfiguration $appConfig;
    private SessionInterface $session;
    private TranslationHelperInterface $translationHelper;

    public function __construct(
        ApplicationConfiguration $appConfig,
        SessionInterface $session,
        TranslationHelperInterface $translationHelper
    ) {
        $this->appConfig = $appConfig;
        $this->session = $session;
        $this->translationHelper = $translationHelper;
    }

    public function add(string $type, string $message, array $messageParameters = []): void
    {
        if ($this->appConfig->isTranslationEnabled()) {
            $message = $this
                ->translationHelper
                ->trans($message, $messageParameters, $this->appConfig->getTranslationCatalog())
            ;
        }
        $this->session->getFlashBag()->add($type, $message);
    }
}
