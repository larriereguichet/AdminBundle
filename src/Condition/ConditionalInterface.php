<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Condition;

interface ConditionalInterface
{
    public function getCondition(): ?string;
}
