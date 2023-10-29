<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Response\Handler\RedirectHandlerInterface;
use LAG\AdminBundle\State\Processor\DataProcessorInterface;
use LAG\AdminBundle\State\Provider\DataProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

readonly class ResourceController
{
    public function __construct(
        private UriVariablesExtractorInterface $uriVariablesExtractor,
        private ContextProviderInterface $contextProvider,
        private DataProviderInterface $dataProvider,
        private DataProcessorInterface $dataProcessor,
        private FormFactoryInterface $formFactory,
        private Environment $environment,
        private RedirectHandlerInterface $redirectionHandler,
        private SerializerInterface $serializer,
    ) {
    }

    public function __invoke(Request $request, OperationInterface $operation): Response
    {
        $uriVariables = $this->uriVariablesExtractor->extractVariables($operation, $request);
        $context = $this->contextProvider->getContext($operation, $request);
        $data = $this->dataProvider->provide($operation, $uriVariables, $context);
        $form = null;

        if ($operation->getFormType() !== null) {
            $form = $this->formFactory->create($operation->getFormType(), $data, $operation->getFormOptions());
            $form->handleRequest($request);

            if ($context['json'] ?? false) {
                $form->submit($request->toArray());
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $this->dataProcessor->process($data, $operation, $uriVariables, $context);

                return $this->redirectionHandler->createRedirectResponse($operation, $data, $context);
            }
        }

        if ($request->getContentTypeFormat() === 'json') {
            $content = $this->serializer->serialize($data, 'json', $operation->getNormalizationContext());

            return new JsonResponse($content, Response::HTTP_OK, [], true);
        }

        return new Response($this->environment->render($operation->getTemplate(), [
            'resource' => $operation->getResource(),
            'operation' => $operation,
            'data' => $data,
            'form' => $form?->createView(),
        ]), $form?->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK);
    }
}
