<?php

namespace LAG\AdminBundle\Admin\Configuration;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\Configuration;
use LAG\AdminBundle\Configuration\ConfigurationInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Ease Admin configuration manipulation.
 */
class AdminConfiguration extends Configuration implements ConfigurationInterface
{
    /**
     * @var ApplicationConfiguration
     */
    protected $applicationConfiguration;

    /**
     * AdminConfiguration constructor.
     *
     * @param ApplicationConfiguration $applicationConfiguration
     */
    public function __construct(ApplicationConfiguration $applicationConfiguration)
    {
        parent::__construct();

        $this->applicationConfiguration = $applicationConfiguration;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // inherited routing configuration from global application configuration
        $routing = $this
            ->applicationConfiguration
            ->getParameter('routing');

        // inherited max per page configuration
        $maxPerPage = $this
            ->applicationConfiguration
            ->getParameter('max_per_page');

        // optional options
        $resolver->setDefaults([
            'actions' => [
                'list' => [],
                'create' => [],
                'edit' => [],
                'delete' => [],
            ],
            'batch' => true,
            'routing_url_pattern' => $routing['url_pattern'],
            'routing_name_pattern' => $routing['name_pattern'],
            'controller' => 'LAGAdminBundle:CRUD',
            'max_per_page' => $maxPerPage,
            'data_provider' => null,
        ]);
        // required options
        $resolver->setRequired([
            'entity',
            'form',
        ]);

        $resolver->setDefault('menu', [
            'main' => [
                'action' => 'list'
            ]
        ]);

        $resolver->setNormalizer('actions', function(Options $options, $actions) {
            $normalizedActions = [];

            foreach ($actions as $name => $action) {

                if ($action === null) {
                    $action = [];
                }
                $normalizedActions[$name] = $action;
            }

            return $normalizedActions;
        });
    }
}
