<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

/**
 * This exception is thrown when the configuration given to the AdminBundle is invalid. this can be when creating the
 * application, an admin, an action or a menu.
 */
class ConfigurationException extends Exception
{
    private array $configuration;

    public function __construct(
        string $resourceType = null,
        string $resourceName = '',
        \Throwable $previous = null,
        array $configuration = []
    ) {
        $typeMessage = 'An error has occurred when resolving a configuration';

        if ($resourceType === 'menu') {
            $typeMessage = 'An error has occurred when resolving the configuration for the menu "%s"';
        }

        if ($resourceType === 'action') {
            $typeMessage = 'An error has occurred when resolving the configuration of the action "%s"';
        }

        if ($resourceType === 'admin') {
            $typeMessage = 'An error has occurred when resolving the configuration of the admin "%s"';
        }
        $message = sprintf($typeMessage, $resourceName);
        $code = 0;

        if ($previous !== null) {
            $message .= ' : '.$previous->getMessage();
            $code = $previous->getCode();
        }

        parent::__construct($message, $code, $previous);
        $this->configuration = $configuration;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}
