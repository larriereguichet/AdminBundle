<?php

namespace LAG\AdminBundle\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Bridge\Doctrine\ORM\DataProvider\ORMDataProvider;
use LAG\AdminBundle\Configuration\Behavior\TranslationConfigurationTrait;
use LAG\AdminBundle\Controller\AdminAction;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Ease Admin configuration manipulation.
 */
class AdminConfiguration extends Configuration
{
    use TranslationConfigurationTrait;

    /**
     * @var ApplicationConfiguration
     */
    protected $application;

    /**
     * @var string
     */
    private $name;

    /**
     * AdminConfiguration constructor.
     */
    public function __construct(string $name, ApplicationConfiguration $application)
    {
        parent::__construct();

        $this->application = $application;
        $this->name = $name;
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
                'class' => $this->application->getParameter('admin_class'),
                'routing_url_pattern' => $this->application->getParameter('routing_url_pattern'),
                'routing_name_pattern' => $this->application->getParameter('routing_name_pattern'),
                'controller' => AdminAction::class,
                'max_per_page' => $this->application->getParameter('max_per_page'),
                'form' => null,
                'form_options' => [],
                'pager' => $this->application->getParameter('pager'),
                'permissions' => $this->application->getParameter('permissions'),
                'string_length' => $this->application->getParameter('string_length'),
                'string_length_truncate' => $this->application->getParameter('string_length_truncate'),
                'date_format' => $this->application->getParameter('date_format'),
                'data_provider' => ORMDataProvider::class,
                'page_parameter' => $this->application->getParameter('page_parameter'),
                'list_template' => $this->application->get('list_template'),
                'edit_template' => $this->application->get('edit_template'),
                'create_template' => $this->application->get('create_template'),
                'delete_template' => $this->application->get('delete_template'),
                'menus' => $this->application->get('menus'),
            ])
            ->setRequired([
                'entity',
            ])
            ->setAllowedTypes('string_length', 'integer')
            ->setAllowedTypes('string_length_truncate', 'string')
            ->setAllowedTypes('page_parameter', 'string')
            ->setAllowedValues('pager', [
                null,
                'pagerfanta',
            ])
            ->setAllowedTypes('menus', ['array', 'null'])
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

        $this->configureTranslation(
            $resolver,
            $this->application->getTranslationPattern(),
            $this->application->getTranslationCatalog()
        );
    }

    public function getName(): string
    {
        return $this->name;
    }
}
