<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\DataProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// TODO remove
/** @deprecated  */
class AdminAction
{
    public function __construct(
        private UriVariablesExtractorInterface $uriVariablesExtractor,
        private ContextProviderInterface $contextProvider,
        private DataProviderInterface $dataProvider,
        private ResponseHandlerInterface $responseHandler,
    ) {
    }

    public function __invoke(Request $request, Admin $admin): Response
    {
        $operation = $admin->getCurrentOperation();
        $uriVariables = $this->uriVariablesExtractor->extractVariables($operation, $request);
        $context = $this->contextProvider->getContext($operation, $request);
        $data = $this->dataProvider->provide($admin, $operation, $uriVariables, $context);

        return $this->responseHandler->handle($operation, $request, $data, $context);
    }
}
