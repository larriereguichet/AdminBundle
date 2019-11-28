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
     */
    public function __construct(ApplicationConfiguration $applicationConfiguration)
    {
        parent::__construct();

        $this->applicationConfiguration = $applicationConfiguration;
    }

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
                'translation' => $this->applicationConfiguration->getParameter('translation'),
                'translation_pattern' => $this->applicationConfiguration->getParameter('translation_pattern'),
                'form' => null,
                'form_options' => [],
                'pager' => $this->applicationConfiguration->getParameter('pager'),
                'permissions' => $this->applicationConfiguration->getParameter('permissions'),
                'string_length' => $this->applicationConfiguration->getParameter('string_length'),
                'string_length_truncate' => $this->applicationConfiguration->getParameter('string_length_truncate'),
                'date_format' => $this->applicationConfiguration->getParameter('date_format'),
                'data_provider' => ORMDataProvider::class,
                'page_parameter' => $this->applicationConfiguration->getParameter('page_parameter'),
                'list_template' => $this->applicationConfiguration->get('list_template'),
                'edit_template' => $this->applicationConfiguration->get('edit_template'),
                'create_template' => $this->applicationConfiguration->get('create_template'),
                'delete_template' => $this->applicationConfiguration->get('delete_template'),
            ])
            ->setRequired([
                'entity',
            ])
            ->setAllowedTypes('string_length', 'integer')
            ->setAllowedTypes('string_length_truncate', 'string')
            ->setAllowedTypes('page_parameter', 'string')
            ->setAllowedTypes('translation', 'boolean')
            ->setAllowedValues('pager', [
                null,
                'pagerfanta',
            ])
            ->setNormalizer('actions', function (Options $options, $actions) {
                $normalizedActions = [];
                $addBatchAction = false;

                foreach ($actions as $name => $action) {
                    // action configuration is an array by default
                    if (null === $action) {
                        $action = [];
                    }
                    $normalizedActions[$name] = $action;

                    // in list action, if no batch was configured or disabled, we add a batch action
                    if ('list' == $name && (!array_key_exists('batch', $action) || null === $action['batch'])) {
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
