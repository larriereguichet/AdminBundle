<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Security;

use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Context\OperationContextInterface;
use LAG\AdminBundle\Security\Voter\OperationPermissionVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class PermissionListener
{
    public function __construct(
        private ParametersExtractorInterface $parametersExtractor,
        private OperationContextInterface $operationContext,
        private Security $security,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($this->parametersExtractor->getOperationName($request) === null) {
            return;
        }
        $operation = $this->operationContext->getOperation();

        if (!$this->security->isGranted(OperationPermissionVoter::RESOURCE_ACCESS, $operation)) {
            throw new AccessDeniedException('You are not allowed to access to this resource');
        }
    }
}
