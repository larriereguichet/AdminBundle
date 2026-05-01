<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

interface RedirectResponseHandlerInterface
{
    /**
     * Create a redirection response according to the operation configuration. It is usually called after a valid form
     * submission.
     *
     * @param array<string, mixed> $context
     */
    public function createRedirectResponse(
        Request $request,
        OperationInterface $operation,
        mixed $data,
        array $context = [],
    ): RedirectResponse;
}
