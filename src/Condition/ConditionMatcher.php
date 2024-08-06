<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Condition;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Workflow\Registry;

final readonly class ConditionMatcher implements ConditionMatcherInterface
{
    public function __construct(
        private Registry $workflowRegistry,
    ) {
    }

    public function matchCondition(mixed $data, string $condition, array $context = [], string $workflow = null): bool
    {
        $context = ['this' => $data] + $context;

        if ($workflow !== null) {
            $workflow = $this->workflowRegistry->get($data, $workflow);
            $context['workflow'] = $workflow;
        }
        $expressionLanguage = new ExpressionLanguage();
        $result = $expressionLanguage->evaluate($condition, $context);

        if (!is_bool($result)) {
            return false;
        }

        return $result;
    }
}
