<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

use function Symfony\Component\String\u;

class ValidationException extends Exception
{
    public function __construct(string $message, ConstraintViolationListInterface $errors)
    {
        $message = u($message);

        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $message = $message->append(\PHP_EOL);

            if ($error->getPropertyPath()) {
                $message = $message->append('"', $error->getPropertyPath(), '"', ' ');
            }
            $message = $message->append($error->getMessage())->append(\PHP_EOL);
        }

        parent::__construct($message->toString());
    }
}
