<?php

namespace LAG\AdminBundle\Message;

/**
 * Messgae handler to handle admin message
 */
interface MessageHandlerInterface
{
    /**
     * Handle an error message
     *
     * @param string $flashMessage
     * @param string|null $logMessage
     */
    public function handleError($flashMessage, $logMessage = null);

    /**
     * Handle a success message
     *
     * @param string $flashMessage
     * @param string|null $logMessage
     */
    public function handleSuccess($flashMessage, $logMessage = null);
}
