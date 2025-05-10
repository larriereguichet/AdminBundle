<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Exception\Operation\MissingOperationTemplateException;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
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

    public function createResponse(OperationInterface $operation, mixed $data, Request $request, array $context = []): Response
    {
        if ($operation->getTemplate() === null) {
            throw new MissingOperationTemplateException('The operation "%s" is missing a template', $operation->getName());
        }
        $resource = $operation->getResource();
        $resourceName = u($resource->getName())->camel()->toString();

        if ($operation instanceof CollectionOperationInterface && !u($resourceName)->endsWith('s')) {
            $inflector = new EnglishInflector();
            $resourceName = $inflector->pluralize($resourceName)[0];
        }
        $context += [
            'resource' => $operation->getResource(),
            'operation' => $operation,
            'data' => $data,
            $resourceName => $data,
        ];

        if ($operation->getBaseTemplate() !== null) {
            $context['baseTemplate'] = $operation->getBaseTemplate();
        }

        return new Response($this->environment->render($operation->getTemplate(), $context), $context['responseCode'] ?? Response::HTTP_OK);
    }
}
