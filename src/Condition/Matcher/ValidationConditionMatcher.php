<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Condition\Matcher;

use LAG\AdminBundle\Condition\ConditionalInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ValidationConditionMatcher implements ConditionMatcherInterface
{
    public function __construct(
        private ConditionMatcherInterface $conditionMatcher,
        private ValidatorInterface $validator,
    ) {
    }

    public function matchCondition(ConditionalInterface $subject, mixed $data, array $context = []): bool
    {
        if (empty($context['validator'])) {
            $context['validator'] = $this->validator;
        }

        return $this->conditionMatcher->matchCondition($subject, $data, $context);
    }
}
