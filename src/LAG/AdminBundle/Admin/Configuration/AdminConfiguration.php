<?php

namespace LAG\AdminBundle\Admin\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Ease Admin configuration manipulation.
 */
class AdminConfiguration extends Configuration
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
        $this->configureDefaultOptions($resolver);
        
        // required options
        $resolver->setRequired([
            'entity',
        ]);
        $resolver->setNormalizer('actions', function(Options $options, $actions) {
            $normalizedActions = [];
            $addBatchAction = false;

            foreach ($actions as $name => $action) {

                // action configuration is an array by default
                if ($action === null) {
                    $action = [];
                }
                $normalizedActions[$name] = $action;

                // in list action, if no batch was configured or disabled, we add a batch action
                if ($name == 'list' && (!array_key_exists('batch', $action) || $action['batch'] === null)) {
                    $addBatchAction = true;
                }
            }

            // add empty default batch action
            if ($addBatchAction) {
                $normalizedActions['batch'] = [];
            }

            return $normalizedActions;
        });
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    protected function configureDefaultOptions(OptionsResolver $resolver)
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
            'translation_pattern' => $this
                ->applicationConfiguration
                ->getParameter('translation')['pattern'],
            'form' => null,
            'form_options' => [],
            'pager' => 'pagerfanta',
            'menu', [
                'main' => [
                    'action' => 'list'
                ]
            ],
        ]);
    }
}