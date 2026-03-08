<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Condition\Matcher;

use LAG\AdminBundle\Condition\ConditionalInterface;

interface ConditionMatcherInterface
{
    /**
     * Return if the given condition contained in a subject is matched. An additional context could be added.
     *
     * @param array<string, mixed> $context
     */
    public function matchCondition(ConditionalInterface $subject, mixed $data, array $context = []): bool;
}
