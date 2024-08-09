<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Security;

use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Security\Voter\OperationPermissionVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class PermissionListener
{
    public function __construct(
        private ResourceContextInterface $resourceContext,
        private Security $security,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->resourceContext->supports($request)) {
            return;
        }
        $operation = $this->resourceContext->getOperation($request);

        if (!$this->security->isGranted(OperationPermissionVoter::RESOURCE_ACCESS, $operation)) {
            throw new AccessDeniedException('You are not allowed to access to this resource');
        }
    }
}
