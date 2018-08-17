<?php

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Bridge\Doctrine\ORM\DataProvider\ORMDataProvider;
use LAG\AdminBundle\Controller\AdminAction;
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
        $resolver
            ->setDefaults([
                'actions' => [
                    'list' => [],
                    'create' => [],
                    'edit' => [],
                    'delete' => [],
                ],
                'batch' => true,
                'class' => $this->applicationConfiguration->getParameter('admin_class'),
                'routing_url_pattern' => $this->applicationConfiguration->getParameter('routing_url_pattern'),
                'routing_name_pattern' => $this->applicationConfiguration->getParameter('routing_name_pattern'),
                'controller' => AdminAction::class,
                'max_per_page' => $this->applicationConfiguration->getParameter('max_per_page'),
                'translation_enabled' => $this->applicationConfiguration->getParameter('translation')['enabled'],
                'translation_pattern' => $this
                    ->applicationConfiguration
                    ->getParameter('translation')['pattern'],
                'form' => null,
                'form_options' => [],
                'pager' => 'pagerfanta',
                'permissions' => $this->applicationConfiguration->getParameter('permissions'),
                'string_length' => $this->applicationConfiguration->getParameter('string_length'),
                'string_length_truncate' => $this->applicationConfiguration->getParameter('string_length_truncate'),
                'date_format' => $this->applicationConfiguration->getParameter('date_format'),
                'data_provider' => ORMDataProvider::class,
                'page_parameter' => $this->applicationConfiguration->getParameter('page_parameter'),
            ])
            ->setRequired([
                'entity',
            ])
            ->setAllowedTypes('string_length', 'integer')
            ->setAllowedTypes('string_length_truncate', 'string')
            ->setAllowedTypes('page_parameter', 'string')
            ->setAllowedTypes('translation_enabled', 'boolean')
            ->setAllowedValues('pager', [
                null,
                'pagerfanta',
            ])
            ->setNormalizer('actions', function (Options $options, $actions) {
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
            })
        ;
    }
}
