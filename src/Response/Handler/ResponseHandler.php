<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Inflector\EnglishInflector;
use Twig\Environment;
use function Symfony\Component\String\u;

final readonly class ResponseHandler implements ResponseHandlerInterface
{
    public function __construct(
        private Environment $environment,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function createResponse(
        Request $request,
        OperationInterface $operation,
        mixed $data,
        ?FormInterface $form = null,
        ?FormInterface $filterForm = null,
        ?GridView $grid = null,
    ): Response {
        $resourceName = $operation->getResource()->getName();
        $responseCode = $form?->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;

        if ($operation instanceof CollectionOperationInterface && !u($resourceName)->endsWith('s')) {
            $inflector = new EnglishInflector();
            $resourceName = $inflector->pluralize($resourceName)[0];
        }
        $context = [
            'resource' => $operation->getResource(),
            'operation' => $operation,
            'data' => $data,
            $resourceName => $data,
        ];

        if ($form !== null) {
            $context['form'] = $form->createView();
        }

        if ($filterForm !== null) {
            $context['filterForm'] = $filterForm->createView();
        }

        if ($grid !== null) {
            $context['grid'] = $grid;
        }

        return new Response($this->environment->render($operation->getTemplate(), $context), $responseCode);
    }

    public function createRedirectResponse(OperationInterface $operation, mixed $data, array $context = []): Response
    {
        if ($operation->getRedirectOperation()) {
            $redirectUrl = $this->urlGenerator->generateFromOperationName(
                $operation->getRedirectResource() ?? $operation->getResource()->getName(),
                $operation->getRedirectOperation(),
                $data,
                $operation->getRedirectApplication() ?? $operation->getResource()->getApplication(),
            );
        } elseif ($operation->getRedirectRoute()) {
            $redirectUrl = $this->urlGenerator->generateFromRouteName(
                $operation->getRedirectRoute(),
                $operation->getRedirectRouteParameters(),
                $data,
            );
        } else {
            $redirectUrl = $this->urlGenerator->generateFromRouteName(
                $operation->getRoute(),
                $operation->getRouteParameters(),
                $data,
            );
        }

        return new RedirectResponse($redirectUrl);
    }
}
