<?php

namespace LAG\AdminBundle\Event\Subscriber;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig_Environment;

class TemplateSubscriber implements EventSubscriberInterface
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var ApplicationConfiguration
     */
    protected $configuration;

    /**
     * TemplateSubscriber constructor.
     *
     * @param Twig_Environment $twig
     * @param ConfigurationFactory $configurationFactory
     */
    public function __construct(Twig_Environment $twig, ConfigurationFactory $configurationFactory)
    {
        $this->twig = $twig;
        $this->configuration = $configurationFactory->getApplicationConfiguration();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'kernelView',
        ];
    }

    /**
     *
     * @return null
     */
    public function kernelView()
    {
        $this->twig->addGlobal('config', $this->configuration);
    }
}
