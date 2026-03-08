<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

use function Symfony\Component\String\u;

class ValidationException extends Exception
{
    public function __construct(string $message, ConstraintViolationListInterface $errors, mixed ...$parameters)
    {
        $errorMessage = u($message);

        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $errorMessage = $errorMessage->append(\PHP_EOL);

            if ($error->getPropertyPath()) {
                $errorMessage = $errorMessage->append($error->getPropertyPath(), ':', ' ');
            }
            $errorMessage = $errorMessage
                ->append('"')
                ->append($error->getMessage())
                ->append('"')
                ->append(\PHP_EOL)
            ;
        }

        parent::__construct((string) $errorMessage, $parameters);
    }
}
