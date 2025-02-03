<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Inflector\EnglishInflector;
use Twig\Environment;

use function Symfony\Component\String\u;

final readonly class TemplateResponseHandler implements ResponseHandlerInterface
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    public function supports(OperationInterface $operation, mixed $data, Request $request, array $context = []): bool
    {
        return $operation->getTemplate() !== null;
    }

    public function createResponse(OperationInterface $operation, mixed $data, Request $request, array $context = []): Response
    {
        $responseCode = ($context['submitted'] ?? false) ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;

        $resource = $operation->getResource();

        $resourceName = u($resource->getName())->camel()->toString();

        if ($operation instanceof CollectionOperationInterface && !u($resourceName)->endsWith('s')) {
            $inflector = new EnglishInflector();
            $resourceName = $inflector->pluralize($resourceName)[0];
        }
        $templateContext = $context + [
            'resource' => $operation->getResource(),
            'operation' => $operation,
            'baseTemplate' => $operation->getBaseTemplate(),
            'data' => $data,
            $resourceName => $data,
        ];

        if (($context['partial'] ?? false) === true) {
            $templateContext['baseTemplate'] = '@LAGAdmin/partial.html.twig';
        }

        return new Response($this->environment->render($operation->getTemplate(), $templateContext), $responseCode);
    }
}
