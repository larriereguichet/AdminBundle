<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcher;
use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Tests\ContainerTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ValidationServicesTest extends TestCase
{
    use ContainerTestTrait;

    #[Test]
    public function servicesExists(): void
    {
        self::assertService(ConditionMatcherInterface::class);
        self::assertNoService(ConditionMatcher::class);
    }

}
