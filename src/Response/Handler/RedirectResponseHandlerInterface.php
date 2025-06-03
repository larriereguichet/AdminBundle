<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

interface RedirectResponseHandlerInterface
{
    /**
     * Create a redirection response according to the operation configuration. It is usually called after a valid form
     * submission.
     */
    public function createRedirectResponse(OperationInterface $operation, mixed $data, array $context = []): RedirectResponse;
}
