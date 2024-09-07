<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DependencyInjection\CompilerPass;

use LAG\AdminBundle\Condition\Matcher\WorkflowConditionMatcher;
use LAG\AdminBundle\State\Processor\WorkflowProcessor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class WorkflowCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('workflow.registry')) {
            $container->removeDefinition(WorkflowConditionMatcher::class);
            $container->removeAlias(WorkflowProcessor::class);
        }
    }
}
