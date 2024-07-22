<?php

namespace LAG\AdminBundle\Condition;

interface ConditionMatcherInterface
{
    public function matchCondition(mixed $data, string $condition, array $context = [], string $workflow = null): bool;
}
