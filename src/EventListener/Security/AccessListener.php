<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Security;

use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Context\OperationContextInterface;
use LAG\AdminBundle\Security\Voter\OperationVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Check if an operation can be displayed. Elsewhere, an AccessDeniedException is thrown.
 */
final readonly class AccessListener
{
    public function __construct(
        private OperationContextInterface $operationContext,
        private Security $security,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$this->operationContext->hasOperation()) {
            return;
        }
        $operation = $this->operationContext->getOperation();

        if (!$this->security->isGranted(OperationVoter::OPERATION_ACCESS, $operation)) {
            throw new AccessDeniedException('You are not allowed to access to this resource');
        }
    }
}
