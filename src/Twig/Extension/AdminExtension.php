<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Security\Helper\SecurityHelper;
use LAG\AdminBundle\Twig\Render\ActionRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminExtension extends AbstractExtension
{
    public function __construct(
        private SecurityHelper $security,
        private ActionRendererInterface $actionRenderer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_operation_allowed', [$this, 'isOperationAllowed']),
            new TwigFunction('lag_admin_action', [$this, 'renderAction'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_operation_url', [$this, 'getOperationUrl'], ['is_safe' => ['html']]),
        ];
    }

    public function isOperationAllowed(string $resourceName, string $operationName): bool
    {
        return $this->security->isOperationAllowed($resourceName, $operationName);
    }

    public function renderAction(Action $action, mixed $data = null, array $options = []): string
    {
        return $this->actionRenderer->render($action, $data, $options);
    }

    public function getOperationUrl(OperationInterface $operation, mixed $data = null): string
    {
        return $this->urlGenerator->generateFromOperationName(
            $operation->getResourceName(),
            $operation->getName(),
            $data,
        );
    }
}
