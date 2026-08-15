<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Exception;

use LAG\AdminBundle\Exception\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

final class ValidationExceptionTest extends TestCase
{
    #[Test]
    public function itBuildsMessageWithPropertyPath(): void
    {
        $violations = new ConstraintViolationList([
            new ConstraintViolation('must not be blank', null, [], null, 'name', null),
        ]);

        $exception = new ValidationException('Validation failed', $violations);

        self::assertStringContainsString('Validation failed', $exception->getMessage());
        self::assertStringContainsString('name:', $exception->getMessage());
        self::assertStringContainsString('"must not be blank"', $exception->getMessage());
    }

    #[Test]
    public function itBuildsMessageWithoutPropertyPath(): void
    {
        $violations = new ConstraintViolationList([
            new ConstraintViolation('something went wrong', null, [], null, '', null),
        ]);

        $exception = new ValidationException('Validation failed', $violations);

        self::assertStringContainsString('"something went wrong"', $exception->getMessage());
        self::assertStringNotContainsString(':', $exception->getMessage());
    }
}
