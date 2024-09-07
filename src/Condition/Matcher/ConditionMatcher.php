<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Condition\Matcher;

use LAG\AdminBundle\Condition\ConditionalInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\ExpressionLanguage;

final readonly class ConditionMatcher implements ConditionMatcherInterface
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function matchCondition(ConditionalInterface $subject, mixed $data, array $context = []): bool
    {
        if ($subject->getCondition() === null) {
            return true;
        }
        $context = [
            'this' => $data,
            'data' => $data,
            'object' => $context['object'] ?? $data,
            'auth_checker' => $this->authorizationChecker,
        ] + $context;

        $expressionLanguage = new ExpressionLanguage();

        $result = $expressionLanguage->evaluate($subject->getCondition(), $context);

        if (!\is_bool($result)) {
            return false;
        }

        return $result;
    }
}
