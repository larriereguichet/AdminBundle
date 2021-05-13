<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\Configuration\ConfigurationFactoryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\Security\Helper\SecurityHelper;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoutingExtension extends AbstractExtension
{
    private UrlGeneratorInterface $urlGenerator;
    private SecurityHelper $security;
    private ApplicationConfiguration $appConfig;
    private ConfigurationFactoryInterface $configurationFactory;
    private RouterInterface $router;
    private ResourceRegistryInterface $resourceRegistry;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        SecurityHelper $security,
        ApplicationConfiguration $appConfig,
        ConfigurationFactoryInterface $configurationFactory,
        RouterInterface $router,
        ResourceRegistryInterface $resourceRegistry
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
        $this->appConfig = $appConfig;
        $this->configurationFactory = $configurationFactory;
        $this->router = $router;
        $this->resourceRegistry = $resourceRegistry;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_route', [$this, 'generateAdminUrl']),
            new TwigFunction('admin_url', [$this, 'getAdminUrl']),
        ];
    }

    public function getAdminUrl(string $adminName, string $actionName, $data = null): string
    {
        if (!$this->security->isActionAllowed($adminName, $actionName)) {
            throw new Exception(sprintf('The action "%s" is not allowed for the admin "%s"', $actionName, $adminName));
        }
        $routeParameters = [];
        $routeName = $this->appConfig->getRouteName($adminName, $actionName);

        if ($data !== null) {
            $resource = $this->resourceRegistry->get($adminName);
            $adminConfiguration = $this
                ->configurationFactory
                ->createAdminConfiguration($adminName, $resource->getConfiguration())
            ;
            $accessor = PropertyAccess::createPropertyAccessor();
            $actionConfiguration = $this->configurationFactory->createActionConfiguration(
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
}
