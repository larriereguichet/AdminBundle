<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Grid;

use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidGridConfigurationException extends Exception
{
    public function __construct(string $gridName, ConstraintViolationListInterface $errors)
    {
        $message = sprintf('The grid "%s" configuration is invalid:', $gridName);

        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $message .= \PHP_EOL.$error->getPropertyPath().': '.$error->getMessage()->__toString();
        }

        parent::__construct($message);
    }
}
