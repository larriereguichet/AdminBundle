<?php

namespace LAG\AdminBundle\Exception;

use Throwable;

/**
 * This exception is thrown when the configuration given to the AdminBundle is invalid. this can be when creating the
 * application, an admin, an action or a menu.
 */
class ConfigurationException extends Exception
{
    /**
     * @var array
     */
    private $configuration;

    public function __construct(
        string $type = null,
        string $typeName = '',
        $code = 0,
        Throwable $previous = null,
        array $configuration = []
    ) {
        $typeMessage = 'An error has occurred when resolving a configuration';

        if ($type === 'menu') {
            $typeMessage = 'An error has occurred when resolving the configuration for the menu "%s"';
        }

        if ($type === 'action') {
            $typeMessage = 'An error has occurred when resolving the configuration of the action "%s"';
        }

        if ($type === 'admin') {
            $typeMessage = 'An error has occurred when resolving the configuration of the admin "%s"';
        }
        $message = sprintf($typeMessage, $typeName);

        if ($previous) {
            $message .= ' : '.$previous->getMessage();
        }

        parent::__construct($message, $code, $previous);
        $this->configuration = $configuration;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}
