<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Condition\Matcher;

use LAG\AdminBundle\Condition\ConditionalInterface;

interface ConditionMatcherInterface
{
    public function matchCondition(ConditionalInterface $subject, mixed $data, array $context = []): bool;
}
