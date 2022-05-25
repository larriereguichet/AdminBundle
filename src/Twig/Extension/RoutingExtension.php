<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Action\Factory\ActionConfigurationFactoryInterface;
use LAG\AdminBundle\Admin\Factory\AdminConfigurationFactoryInterface;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Resolver\RoutingUrlResolverInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Security\Helper\SecurityHelper;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoutingExtension extends AbstractExtension
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private SecurityHelper $security,
        private ApplicationConfiguration $applicationConfiguration,
        private AdminConfigurationFactoryInterface $adminConfigurationFactory,
        private ActionConfigurationFactoryInterface $actionConfigurationFactory,
        private RouterInterface $router,
        private ResourceRegistryInterface $resourceRegistry,
        private RoutingUrlResolverInterface $urlResolver,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_route', [$this, 'generateAdminUrl']),
            new TwigFunction('admin_url', [$this, 'getAdminUrl']),
            new TwigFunction('admin_link', [$this, 'renderAdminLink']),
        ];
    }

    public function getAdminUrl(string $adminName, string $actionName, $data = null): string
    {
        if (!$this->security->isActionAllowed($adminName, $actionName)) {
            throw new Exception(sprintf('The action "%s" is not allowed for the admin "%s"', $actionName, $adminName));
        }
        $routeParameters = [];
        $routeName = $this->applicationConfiguration->getRouteName($adminName, $actionName);

        if ($data !== null) {
            $resource = $this->resourceRegistry->get($adminName);
            $adminConfiguration = $this
                ->adminConfigurationFactory
                ->create($adminName, $resource->getConfiguration())
            ;
            $accessor = PropertyAccess::createPropertyAccessor();
            $actionConfiguration = $this->actionConfigurationFactory->create(
                $adminName,
                $actionName,
                $adminConfiguration->getAction($actionName)
            );

            foreach ($actionConfiguration->getRouteParameters() as $name => $requirements) {
                $routeParameters[$name] = $accessor->getValue($data, $name);
            }
        }

        return $this->router->generate($routeName, $routeParameters);
    }

    public function generateAdminUrl(string $routeName, array $routeParameters = [], object $data = null): string
    {
        return $this->urlGenerator->generateFromRouteName($routeName, $routeParameters, $data);
    }

    public function renderAdminLink(array $linkOptions, object $data = null): string
    {
        return $this->urlResolver->resolve($linkOptions, $data);
    }
}
