<?php

namespace LAG\AdminBundle\Admin\Message;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class MessageHandler
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * ErrorHandler constructor
     *
     * @param LoggerInterface $logger
     * @param Session $session
     * @param TranslatorInterface $translator
     */
    public function __construct(LoggerInterface $logger, Session $session, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * Log message and create flash message
     *
     * @param $flashMessage
     * @param $logMessage
     */
    public function handleError($flashMessage, $logMessage = null)
    {
        $this
            ->session
            ->getFlashBag()
            ->add('error', $this->translator->trans($flashMessage));

        if ($logMessage) {
            $this
                ->logger
                ->error($logMessage);
        }
    }

    /**
     * @param $flashMessage
     * @param null $logMessage
     */
    public function handleSuccess($flashMessage, $logMessage = null)
    {
        $this
            ->session
            ->getFlashBag()
            ->add('info', $this->translator->trans($flashMessage));

        if ($logMessage) {
            $this
                ->logger
                ->info($logMessage);
        }
    }
}
